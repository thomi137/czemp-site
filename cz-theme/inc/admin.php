<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Thumbnail column in the Werke list
add_filter('manage_artwork_posts_columns', function ($columns) {
    $new = ['cb' => $columns['cb'], 'artwork_thumb' => ''];
    unset($columns['cb']);
    return array_merge($new, $columns);
});

add_action('manage_artwork_posts_custom_column', function ($column, $post_id) {
    if ($column !== 'artwork_thumb') return;
    $thumb = get_the_post_thumbnail($post_id, [60, 60]);
    if ($thumb) {
        echo '<div style="width:60px;height:60px;overflow:hidden;border-radius:4px">' . $thumb . '</div>';
    }
}, 10, 2);

add_action('admin_head', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'artwork') return;
    echo '<style>.column-artwork_thumb { width: 70px; }</style>';
});

// Collection dropdown filter in the Werke list and the Media Library
add_action('restrict_manage_posts', function ($post_type) {
    if (!in_array($post_type, ['artwork', 'attachment'], true)) return;
    $terms = get_terms(['taxonomy' => 'collection', 'hide_empty' => false]);
    if (empty($terms) || is_wp_error($terms)) return;

    $default  = ($post_type === 'attachment') ? get_user_meta(get_current_user_id(), 'cz_default_collection', true) : '';
    $selected = $_GET['collection'] ?? ($default ?: 'all');

    echo '<select name="collection"><option value="all"' . selected($selected, 'all', false) . '>Alle Kollektionen</option>';
    foreach ($terms as $term) {
        printf('<option value="%s"%s>%s</option>',
            esc_attr($term->slug),
            selected($selected, $term->slug, false),
            esc_html($term->name)
        );
    }
    echo '</select>';
});

// The "all collections" sentinel value ("all") must never be filtered as a
// real term slug — otherwise the list returns 0 results instead of
// everything. The user's default collection is only applied in the Media
// Library, and only as long as they haven't set the filter themselves.
add_action('pre_get_posts', function ($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    $post_type = $query->get('post_type');
    if (!in_array($post_type, ['artwork', 'attachment'], true)) {
        return;
    }

    if (isset($_GET['collection'])) {
        if ($_GET['collection'] === 'all') {
            $query->set('collection', '');
        }
        return;
    }

    if ($post_type !== 'attachment') {
        return;
    }

    $default = get_user_meta(get_current_user_id(), 'cz_default_collection', true);
    if ($default) {
        $query->set('collection', $default);
    }
});

// Default collection for the Media Library: a field on the user's own
// profile (Users > Profile), not a separate settings page — every user
// can set their own preselection
foreach (['show_user_profile', 'edit_user_profile'] as $hook) {
    add_action($hook, function ($user) {
        $terms   = get_terms(['taxonomy' => 'collection', 'hide_empty' => false]);
        $current = get_user_meta($user->ID, 'cz_default_collection', true);
        ?>
        <h2>Media-Bibliothek</h2>
        <table class="form-table">
            <tr>
                <th><label for="cz-default-collection">Standard-Kollektion</label></th>
                <td>
                    <select name="cz_default_collection" id="cz-default-collection">
                        <option value="">— Keine (alle anzeigen) —</option>
                        <?php if (!is_wp_error($terms)) foreach ($terms as $term) : ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current, $term->slug); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Wird beim Öffnen der Media-Bibliothek als Filter vorausgewählt.</p>
                </td>
            </tr>
        </table>
        <?php
    });
}

foreach (['personal_options_update', 'edit_user_profile_update'] as $hook) {
    add_action($hook, function ($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }
        update_user_meta($user_id, 'cz_default_collection', sanitize_title($_POST['cz_default_collection'] ?? ''));
    });
}

// Default the Media Library to List view (with the collection column/filter
// instead of the unsorted grid view). Only takes effect as long as the user
// hasn't switched to grid view themselves — that choice is then saved as a
// real user option.
add_filter('get_user_option_media_library_mode', function ($result) {
    return $result ?: 'list';
});

// Let Editors manage the Navigation menus (e.g. "Footer Menu") without
// reopening the Site Editor's Templates/Template Parts/Global Styles for
// them. WordPress core has no capability for "navigation only" — every
// one of wp_navigation's, wp_template's, wp_template_part's and
// wp_global_styles' edit/create/delete/publish capabilities are all
// hard-mapped to the single `edit_theme_options` capability (verified via
// get_post_type_object(...)->cap), so granting it to Editors is the only
// way to unlock Navigation editing, and the map_meta_cap + REST filters
// below claw back write access to the other three post types from anyone
// who isn't an administrator. This exists specifically so the "Attempt
// recovery" + autosave incident that overrode theme templates with broken
// markup can't happen again through her account.
add_action('init', function () {
    $editor = get_role('editor');
    if ($editor && !$editor->has_cap('edit_theme_options')) {
        $editor->add_cap('edit_theme_options');
    }
});

function cz_theme_locked_post_types() {
    return ['wp_template', 'wp_template_part', 'wp_global_styles'];
}

add_filter('map_meta_cap', function ($caps, $cap, $user_id, $args) {
    if (!in_array($cap, ['edit_post', 'delete_post', 'publish_post'], true) || empty($args[0])) {
        return $caps;
    }
    $post = get_post($args[0]);
    if (!$post || !in_array($post->post_type, cz_theme_locked_post_types(), true)) {
        return $caps;
    }
    return user_can($user_id, 'administrator') ? $caps : ['do_not_allow'];
}, 10, 4);

// Covers *creating* new templates/parts/styles too, where map_meta_cap
// never sees a post type to check against (core collapses "create_posts"
// straight to edit_theme_options before it gets there).
add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    if (current_user_can('administrator') || $request->get_method() === 'GET') {
        return $result;
    }
    if (preg_match('#^/wp/v2/(templates|template-parts|global-styles)#', $request->get_route())) {
        return new WP_Error(
            'rest_forbidden',
            'Nur Administratoren können Templates, Template-Teile oder globale Stile bearbeiten.',
            ['status' => 403]
        );
    }
    return $result;
}, 10, 3);
