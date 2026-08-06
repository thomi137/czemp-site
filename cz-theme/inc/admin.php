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
