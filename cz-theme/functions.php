<?php
// Copyright (c) 2026 Thomas Prosser. All rights reserved.
/**
 * Claudia Zemp functions and definitions.
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @author Thomas Prosser
 * @package czemp
 * @version 1.0
 * @since 1.0
 */

/*
if ( ! function_exists( 'set_custom_default_logo' ) ) :
	function set_custom_default_logo() {
		if (!has_custom_logo()) {
			// Replace 'default-logo.png' with the correct path or URL to your default logo
			$default_logo = get_stylesheet_directory_uri() . '/assets/images/cz_Logo_without_text.webp';
			return '<img src="' . esc_url($default_logo) . '" alt="Default Logo" "width=100">';
		}
		return '';
	}
	add_filter('get_custom_logo', 'set_custom_default_logo');
endif;
*/

// Theme support
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/global.css');
});

// Register custom blocks
add_action('init', function () {
    register_block_type(get_stylesheet_directory() . '/blocks/gallery-item');
    register_block_type(get_stylesheet_directory() . '/blocks/artwork-list-item');
    register_block_type(get_stylesheet_directory() . '/blocks/sticky-nav');
    register_block_type(get_stylesheet_directory() . '/blocks/collection-subcategories');
    register_block_type(get_stylesheet_directory() . '/blocks/breadcrumbs');
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'cz-gallery-item-view',
        get_stylesheet_directory_uri() . '/blocks/gallery-item/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/gallery-item/view.js'),
        true
    );
    wp_enqueue_script(
        'cz-sticky-nav-view',
        get_stylesheet_directory_uri() . '/blocks/sticky-nav/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/sticky-nav/view.js'),
        true
    );
    wp_enqueue_script(
        'cz-header-height',
        get_stylesheet_directory_uri() . '/assets/js/header-height.js',
        [],
        filemtime(get_stylesheet_directory() . '/assets/js/header-height.js'),
        true
    );
});

add_filter('script_loader_tag', function ($tag, $handle) {
    if ($handle === 'cz-sticky-nav-view') {
        return str_replace('<script ', '<script type="module" ', $tag);
    }
    return $tag;
}, 10, 2);

//  Register custom post type: Artwork
add_action('init', function () {
    register_post_type('artwork', [
        'labels' => [
            'name'               => 'Werke',
            'singular_name'      => 'Werk',
            'add_new_item'       => 'Neues Werk hinzufügen',
            'edit_item'          => 'Werk bearbeiten',
            'view_item'          => 'Werk ansehen',
            'search_items'       => 'Werke suchen',
            'not_found'          => 'Keine Werke gefunden',
            'not_found_in_trash' => 'Keine Werke im Papierkorb',
        ],
        'public'       => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-art',
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'galerie'],
    ]);

    register_taxonomy('collection', ['artwork'], [
        'labels' => [
            'name'          => 'Kollektionen',
            'singular_name' => 'Kollektion',
            'add_new_item'  => 'Neue Kollektion',
            'edit_item'     => 'Kollektion bearbeiten',
        ],
        'public'            => true,
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'kollektion'],
        'capabilities' => [
            'manage_terms' => 'edit_posts',
            'edit_terms'   => 'edit_posts',
            'delete_terms' => 'edit_posts',
            'assign_terms' => 'edit_posts',
        ],
    ]);
});

// Migration REST endpoints — token-geschützt, kein Basic Auth nötig
add_action('rest_api_init', function () {
    $token_check = function (WP_REST_Request $req) {
        $token = defined('CZ_MIGRATE_TOKEN') ? CZ_MIGRATE_TOKEN : '';
        return $token && $req->get_header('X-Migrate-Token') === $token
            ? true
            : new WP_Error('rest_forbidden', 'Ungültiger Token.', ['status' => 403]);
    };

    register_rest_route('czemp/v1', '/collection', [
        'methods'             => 'POST',
        'permission_callback' => $token_check,
        'callback'            => function (WP_REST_Request $req) {
            $name   = sanitize_text_field($req->get_param('name'));
            $slug   = sanitize_title($req->get_param('slug') ?: $name);
            $parent = intval($req->get_param('parent'));
            $args   = ['slug' => $slug];
            if ($parent) {
                $args['parent'] = $parent;
            }
            $term = wp_insert_term($name, 'collection', $args);
            if (is_wp_error($term)) {
                return new WP_Error('term_error', $term->get_error_message(), ['status' => 400]);
            }
            return ['id' => $term['term_id'], 'name' => $name, 'slug' => $slug, 'parent' => $parent];
        },
    ]);

    register_rest_route('czemp/v1', '/artwork', [
        'methods'             => 'POST',
        'permission_callback' => $token_check,
        'callback'            => function (WP_REST_Request $req) {
            $post_id = wp_insert_post([
                'post_type'    => 'artwork',
                'post_status'  => 'publish',
                'post_title'   => sanitize_text_field($req->get_param('title')),
                'post_excerpt' => sanitize_textarea_field($req->get_param('excerpt')),
                'post_content' => wp_kses_post($req->get_param('content')),
            ], true);
            if (is_wp_error($post_id)) {
                return new WP_Error('post_error', $post_id->get_error_message(), ['status' => 400]);
            }
            $collection = intval($req->get_param('collection'));
            if ($collection) {
                wp_set_object_terms($post_id, $collection, 'collection');
            }
            $media = intval($req->get_param('featured_media'));
            if ($media) {
                set_post_thumbnail($post_id, $media);
            }
            $price = sanitize_text_field($req->get_param('price'));
            if ($price !== '') {
                update_post_meta($post_id, 'price', $price);
            }
            return ['id' => $post_id, 'title' => get_the_title($post_id)];
        },
    ]);

    register_rest_route('czemp/v1', '/media', [
        'methods'             => 'POST',
        'permission_callback' => $token_check,
        'callback'            => function (WP_REST_Request $req) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $filename = sanitize_file_name($req->get_header('X-Filename') ?: 'upload');
            $data     = $req->get_body();
            $tmp      = wp_tempnam($filename);
            file_put_contents($tmp, $data);

            $file = ['name' => $filename, 'tmp_name' => $tmp, 'error' => 0, 'size' => strlen($data)];
            $id   = media_handle_sideload($file, 0);
            @unlink($tmp);

            if (is_wp_error($id)) {
                return new WP_Error('media_error', $id->get_error_message(), ['status' => 400]);
            }
            return ['id' => $id, 'url' => wp_get_attachment_url($id)];
        },
    ]);
});

// Register price meta for artwork
add_action('init', function () {
    register_post_meta('artwork', 'price', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
    ]);
});

// Vorschaubild + Fokuspunkt für Kollektionen (Term-Meta + Medien-Picker im Admin)
add_action('init', function () {
    register_term_meta('collection', 'thumbnail_id', [
        'type'         => 'integer',
        'single'       => true,
        'show_in_rest' => true,
    ]);
    register_term_meta('collection', 'thumbnail_focal_x', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
        'default'      => 0.5,
    ]);
    register_term_meta('collection', 'thumbnail_focal_y', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
        'default'      => 0.5,
    ]);
});

// Gibt die Vorschaubild-Markup (Bild + Fokuspunkt-Marker) für den Term-Editor zurück
function cz_collection_thumbnail_preview_html($thumbnail_url, $focal_x, $focal_y) {
    if (!$thumbnail_url) {
        return '';
    }
    return sprintf(
        '<div class="cz-focal-point-picker"><img src="%s" alt=""><div class="cz-focal-point-marker" style="left:%s%%;top:%s%%;"></div></div>',
        esc_url($thumbnail_url),
        esc_attr($focal_x * 100),
        esc_attr($focal_y * 100)
    );
}

add_action('collection_add_form_fields', function () {
    ?>
    <div class="form-field">
        <label for="cz-collection-thumbnail-button">Vorschaubild</label>
        <input type="hidden" name="thumbnail_id" id="cz-collection-thumbnail-id" value="">
        <input type="hidden" name="thumbnail_focal_x" id="cz-collection-thumbnail-focal-x" value="0.5">
        <input type="hidden" name="thumbnail_focal_y" id="cz-collection-thumbnail-focal-y" value="0.5">
        <div id="cz-collection-thumbnail-preview"></div>
        <button type="button" class="button" id="cz-collection-thumbnail-button">Bild auswählen</button>
        <button type="button" class="button" id="cz-collection-thumbnail-remove" style="display:none;">Entfernen</button>
        <p>Wird für die Unterkategorie-Kacheln auf der übergeordneten Kollektionsseite verwendet. Klick ins Bild, um den Bildausschnitt festzulegen.</p>
    </div>
    <?php
});

add_action('collection_edit_form_fields', function ($term) {
    $thumbnail_id  = (int) get_term_meta($term->term_id, 'thumbnail_id', true);
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';
    $focal_x       = get_term_meta($term->term_id, 'thumbnail_focal_x', true);
    $focal_y       = get_term_meta($term->term_id, 'thumbnail_focal_y', true);
    $focal_x       = ($focal_x === '') ? 0.5 : (float) $focal_x;
    $focal_y       = ($focal_y === '') ? 0.5 : (float) $focal_y;
    ?>
    <tr class="form-field">
        <th scope="row"><label for="cz-collection-thumbnail-button">Vorschaubild</label></th>
        <td>
            <input type="hidden" name="thumbnail_id" id="cz-collection-thumbnail-id" value="<?php echo esc_attr($thumbnail_id); ?>">
            <input type="hidden" name="thumbnail_focal_x" id="cz-collection-thumbnail-focal-x" value="<?php echo esc_attr($focal_x); ?>">
            <input type="hidden" name="thumbnail_focal_y" id="cz-collection-thumbnail-focal-y" value="<?php echo esc_attr($focal_y); ?>">
            <div id="cz-collection-thumbnail-preview"><?php echo cz_collection_thumbnail_preview_html($thumbnail_url, $focal_x, $focal_y); ?></div>
            <button type="button" class="button" id="cz-collection-thumbnail-button">Bild auswählen</button>
            <button type="button" class="button" id="cz-collection-thumbnail-remove" style="<?php echo $thumbnail_id ? '' : 'display:none;'; ?>">Entfernen</button>
            <p class="description">Wird für die Unterkategorie-Kacheln auf der übergeordneten Kollektionsseite verwendet. Klick ins Bild, um den Bildausschnitt festzulegen.</p>
        </td>
    </tr>
    <?php
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['edit-tags.php', 'term.php'], true)) {
        return;
    }
    if (($_GET['taxonomy'] ?? '') !== 'collection') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style(
        'cz-collection-thumbnail-picker',
        get_stylesheet_directory_uri() . '/assets/css/collection-thumbnail-picker.css',
        [],
        filemtime(get_stylesheet_directory() . '/assets/css/collection-thumbnail-picker.css')
    );
    wp_enqueue_script(
        'cz-collection-thumbnail-picker',
        get_stylesheet_directory_uri() . '/assets/js/collection-thumbnail-picker.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/assets/js/collection-thumbnail-picker.js'),
        true
    );
});

foreach (['create_collection', 'edited_collection'] as $hook) {
    add_action($hook, function ($term_id) {
        if (isset($_POST['thumbnail_id'])) {
            $thumbnail_id = absint($_POST['thumbnail_id']);
            if ($thumbnail_id) {
                update_term_meta($term_id, 'thumbnail_id', $thumbnail_id);
            } else {
                delete_term_meta($term_id, 'thumbnail_id');
            }
        }
        if (isset($_POST['thumbnail_focal_x'], $_POST['thumbnail_focal_y'])) {
            update_term_meta($term_id, 'thumbnail_focal_x', max(0, min(1, (float) $_POST['thumbnail_focal_x'])));
            update_term_meta($term_id, 'thumbnail_focal_y', max(0, min(1, (float) $_POST['thumbnail_focal_y'])));
        }
    });
}

// Unterkategorien: Artwork-Query der Elternseite nicht mit Werken der
// Kind-Kategorien mischen (die haben jetzt ihre eigenen Übersichtskacheln)
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !is_tax('collection')) {
        return;
    }

    $query->set('tax_query', [
        'relation' => 'AND',
        [
            'taxonomy'         => 'collection',
            'field'            => 'term_id',
            'terms'            => [get_queried_object_id()],
            'include_children' => false,
        ],
    ]);
});

// WebP-Ausgabe für alle neu generierten Bildgrössen
add_filter('image_editor_output_format', function ($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    $formats['image/gif']  = 'image/webp';
    return $formats;
});

// Flush rewrite rules once after activation
add_action('after_switch_theme', function () {
    // Post type & taxonomy are registered via 'init'; flush after.
    flush_rewrite_rules();
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'czemp-global',
        get_stylesheet_directory_uri() . '/assets/css/global.css',
        ['wp-block-library'],
        filemtime(get_stylesheet_directory() . '/assets/css/global.css')
    );
});

// Vorschaubild-Spalte in der Werke-Liste
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

// Kollektion-Dropdown-Filter in der Werke-Liste
add_action('restrict_manage_posts', function ($post_type) {
    if ($post_type !== 'artwork') return;
    $terms = get_terms(['taxonomy' => 'collection', 'hide_empty' => false]);
    if (empty($terms) || is_wp_error($terms)) return;
    $selected = $_GET['collection'] ?? '';
    echo '<select name="collection"><option value="">Alle Kollektionen</option>';
    foreach ($terms as $term) {
        printf('<option value="%s"%s>%s</option>',
            esc_attr($term->slug),
            selected($selected, $term->slug, false),
            esc_html($term->name)
        );
    }
    echo '</select>';
});

// Mark "Galerie" nav item as active on artwork and collection pages
add_filter('render_block', function (string $block_content, array $block) {
    if ($block['blockName'] !== 'core/navigation-link') {
        return $block_content;
    }
    if (!is_singular('artwork') && !is_post_type_archive('artwork') && !is_tax('collection')) {
        return $block_content;
    }
    $url          = trailingslashit($block['attrs']['url'] ?? '');
    $gallery_page = get_page_by_path('gallery');
    if (!$gallery_page || empty($url)) {
        return $block_content;
    }
    if (trailingslashit(get_permalink($gallery_page->ID)) === $url) {
        $block_content = str_replace(
            'class="wp-block-navigation-item ',
            'class="wp-block-navigation-item current-menu-item ',
            $block_content
        );
    }
    return $block_content;
}, 10, 2);

// Cap query-pagination-numbers to at most 3 page-number links, centered on the current page
add_filter('render_block', function (string $block_content, array $block) {
    if ($block['blockName'] !== 'core/query-pagination-numbers') {
        return $block_content;
    }
    if (!preg_match('/class="[^"]*page-numbers current[^"]*"[^>]*>(\d+)</', $block_content, $m)) {
        return $block_content;
    }
    $current    = (int) $m[1];
    $max_shown  = 3;
    $half       = (int) floor(($max_shown - 1) / 2);
    $min        = max(1, $current - $half);
    $max        = $min + $max_shown - 1;

    return preg_replace_callback(
        '/<(a|span)\b[^>]*class="[^"]*page-numbers[^"]*"[^>]*>(\d+)<\/\1>/',
        function ($el) use ($min, $max) {
            $num = (int) $el[2];
            return ($num >= $min && $num <= $max) ? $el[0] : '';
        },
        $block_content
    );
}, 10, 2);
