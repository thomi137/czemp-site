<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Drag-and-drop ordering for Werke within a Kollektion. Built
// incrementally — see context/history/ for the delivered pieces of this
// feature. (Posts are sorted by date + archived past their end date
// instead — see blocks/event-archive/render.php — not drag-and-drop.)
//
// Order is stored as an ordered array of artwork post IDs in term meta
// (`_cz_artwork_order`) on the collection term itself — not WordPress's
// native `menu_order` column. An artwork isn't guaranteed to belong to
// only one Kollektion, and `menu_order` is a single value per post: two
// collections sharing an artwork would fight over the same number,
// dragging it in one collection's list silently reordering it in the
// other. Keeping the order on the *term* instead means each collection's
// sequence is independent no matter how many collections an artwork is
// in. No new database table either — WP_Query sorts a specific ID list
// natively via `orderby => 'post__in'`, no custom SQL joins needed.

// The stored order is only ever a hint, reconciled against the term's
// actual current membership on every read: anything in the stored list
// that's no longer a member is dropped, and anything a member but not yet
// in the stored list (newly tagged, or re-tagged from another Kollektion)
// is appended at the end, newest first. This is why there's no separate
// "append new items to the end" write-time hook anywhere in this file —
// it's just computed fresh whenever the order is needed, the same way
// blocks/event-archive/render.php computes "has this event ended" fresh
// on every render instead of storing a flag.
function cz_sortable_get_order($term_id) {
    $stored = get_term_meta($term_id, '_cz_artwork_order', true);
    $stored = is_array($stored) ? array_map('absint', $stored) : [];

    $members = get_posts([
        'post_type'      => 'artwork',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [[
            'taxonomy'         => 'collection',
            'field'            => 'term_id',
            'terms'            => [$term_id],
            'include_children' => false,
        ]],
    ]);

    $known = array_values(array_intersect($stored, $members));
    $new   = array_values(array_diff($members, $known));
    return array_merge($known, $new);
}

// Filtered to a specific Kollektion: sort by the stored (and
// live-reconciled) order, and show the whole Kollektion on one page —
// dragging across pages isn't supported, and Werke/Kollektion counts here
// are modest enough that this is never actually a large page. Priority 20
// so this runs after inc/admin.php's own collection-filter query wiring
// (default priority 10) has already resolved the "all" sentinel. "All" is
// left on its normal default order: dragging across multiple collections'
// interleaved items has no coherent single "position" meaning.
add_action('pre_get_posts', function ($query) {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'artwork') {
        return;
    }
    $slug = $query->get('collection');
    if (!$slug) {
        return;
    }
    $term = get_term_by('slug', $slug, 'collection');
    if (!$term) {
        return;
    }
    if (empty($_GET['orderby'])) {
        $order = cz_sortable_get_order($term->term_id);
        if ($order) {
            $query->set('post__in', $order);
            $query->set('orderby', 'post__in');
        }
    }
    $query->set('posts_per_page', -1);
}, 20);

// Persists a drag-and-drop reorder (the UI itself lives in the enqueued
// admin JS below). Registered unconditionally, unlike inc/rest-api.php's
// migration routes — this is a permanent, cookie-authenticated admin
// feature, not a temporary headless-script surface, so it doesn't belong
// behind CZ_MIGRATE_TOKEN and doesn't share that file's no-capability-check
// design. rest_cookie_check_errors() (WordPress core, always active for
// cookie-authenticated REST requests) already validates the X-WP-Nonce
// header before permission_callback below even runs, so there's no
// wp_verify_nonce() call here — that's the classic-AJAX/meta-box-save
// idiom, not the REST one.
add_action('rest_api_init', function () {
    register_rest_route('czemp/v1', '/reorder', [
        'methods'             => 'POST',
        'permission_callback' => function (WP_REST_Request $request) {
            $term = get_term((int) $request->get_param('term_id'), 'collection');
            if (!$term || is_wp_error($term)) {
                return new WP_Error('rest_invalid_param', 'Ungültige Kollektion.', ['status' => 400]);
            }
            // Matches the 'assign_terms' capability the collection
            // taxonomy is itself registered with (inc/post-types.php).
            return current_user_can('edit_posts');
        },
        'callback' => function (WP_REST_Request $request) {
            $term_id = (int) $request->get_param('term_id');
            $order   = $request->get_param('order');
            if (!is_array($order) || !$order) {
                return new WP_Error('rest_invalid_param', 'Ungültige Reihenfolge.', ['status' => 400]);
            }

            // Validate every ID fully before writing anything — a partial
            // reorder (some rows written, some not) would leave the stored
            // order matching neither the old nor the intended new
            // sequence, which is worse than rejecting the whole request.
            $ids = array_map('absint', $order);
            foreach ($ids as $id) {
                if (!$id || get_post_type($id) !== 'artwork') {
                    return new WP_Error(
                        'rest_invalid_param',
                        sprintf('Ungültige Werk-ID: %d.', $id),
                        ['status' => 400]
                    );
                }
                if (!current_user_can('edit_post', $id)) {
                    return new WP_Error(
                        'rest_forbidden',
                        sprintf('Keine Berechtigung für Werk %d.', $id),
                        ['status' => 403]
                    );
                }
                if (!has_term($term_id, 'collection', $id)) {
                    return new WP_Error(
                        'rest_invalid_param',
                        sprintf('Werk %d gehört nicht zu dieser Kollektion.', $id),
                        ['status' => 400]
                    );
                }
            }

            update_term_meta($term_id, '_cz_artwork_order', $ids);

            return ['success' => true, 'count' => count($ids)];
        },
    ]);
});

// Shared by the drag-handle column and the JS-enqueue gate below, so they
// can't ever disagree about whether a Kollektion is actually filtered.
// The 'all' sentinel (see inc/admin.php's own collection dropdown) must
// never count as "filtered" — dragging across multiple collections'
// interleaved items has no coherent single "position" meaning.
function cz_sortable_active_collection() {
    $slug = $_GET['collection'] ?? '';
    if (!$slug || $slug === 'all') {
        return null;
    }
    $term = get_term_by('slug', $slug, 'collection');
    return $term ?: null;
}

// Drag-handle column in the Werke list — same shape as inc/admin.php's
// artwork_thumb column. Always rendered so there's no layout shift
// between "All" and a filtered view; visually/functionally inert
// (dimmed, not-allowed cursor, explanatory tooltip) unless a Kollektion
// is actually filtered.
add_filter('manage_artwork_posts_columns', function ($columns) {
    $new = ['cb' => $columns['cb'], 'cz_drag_handle' => ''];
    unset($columns['cb']);
    return array_merge($new, $columns);
});

add_action('manage_artwork_posts_custom_column', function ($column, $post_id) {
    if ($column !== 'cz_drag_handle') {
        return;
    }
    $active = (bool) cz_sortable_active_collection();
    printf(
        '<span class="cz-drag-handle%s" title="%s" aria-hidden="true"></span>',
        $active ? '' : ' cz-drag-handle--inactive',
        $active
            ? esc_attr('Ziehen zum Sortieren')
            : esc_attr('Nach einer Kollektion filtern, um zu sortieren')
    );
}, 10, 2);

add_action('admin_head', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'artwork' || $screen->base !== 'edit') {
        return;
    }
    echo '<style>
        .column-cb,
        .column-cz_drag_handle { width: 30px; text-align: center; vertical-align: middle; }
        /* Six-dot grip (2 columns x 3 rows) instead of a dashicon — a
           single-dot radial-gradient tile, repeated by the browser to
           exactly fill a 12x18px box (2 x 6px columns, 3 x 6px rows).
           currentColor so the --inactive dimming below still applies. */
        .cz-drag-handle {
            display: inline-block;
            width: 12px;
            height: 18px;
            vertical-align: middle;
            cursor: grab;
            color: #787c82;
            background-image: radial-gradient(circle, currentColor 1.5px, transparent 1.6px);
            background-size: 6px 6px;
            background-repeat: repeat;
        }
        /* pointer-events: none is the actual enforcement here — it stops
           the handle from ever receiving a mousedown, so no sortable()
           binding (current, future, or forced from the console) can turn
           it into a drag. cursor/opacity below are just the visual cue;
           they are not sufficient alone to keep this inert. */
        .cz-drag-handle--inactive { cursor: not-allowed; opacity: .3; pointer-events: none; }
        #the-list.ui-sortable .cz-drag-handle { cursor: grabbing; }
    </style>';
});

// Enqueued unconditionally on the Werke list screen (not only when a
// Kollektion is filtered) so the column above always has a consistent
// explanation for why it's inert — czSortable.termId is what actually
// gates the sortable() binding in the JS itself.
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'edit.php') {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'artwork') {
        return;
    }
    $term = cz_sortable_active_collection();
    wp_enqueue_script(
        'cz-sortable-list',
        get_stylesheet_directory_uri() . '/build/js/sortable-list.js',
        ['jquery', 'jquery-ui-sortable'],
        filemtime(get_stylesheet_directory() . '/build/js/sortable-list.js'),
        true
    );
    wp_localize_script('cz-sortable-list', 'czSortable', [
        'restUrl'        => esc_url_raw(rest_url('czemp/v1/reorder')),
        'nonce'          => wp_create_nonce('wp_rest'),
        'termId'         => $term ? $term->term_id : 0,
        'orderIsDefault' => empty($_GET['orderby']),
    ]);
});
