<?php
/**
 * Claudia Zemp functions and definitions.
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @author Thomas Prosser
 * @package czemp
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
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'cz-sticky-nav-view',
        get_stylesheet_directory_uri() . '/blocks/sticky-nav/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/sticky-nav/view.js'),
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
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'kollektion'],
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
            $name = sanitize_text_field($req->get_param('name'));
            $slug = sanitize_title($req->get_param('slug') ?: $name);
            $term = wp_insert_term($name, 'collection', ['slug' => $slug]);
            if (is_wp_error($term)) {
                return new WP_Error('term_error', $term->get_error_message(), ['status' => 400]);
            }
            return ['id' => $term['term_id'], 'name' => $name, 'slug' => $slug];
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
