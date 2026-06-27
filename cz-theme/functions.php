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

// Enqueue styles
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'czemp-global',
        get_stylesheet_directory_uri() . '/assets/css/global.css',
        [],
        wp_get_theme()->get('Version')
    );
});

// Register custom blocks
add_action('init', function () {
    register_block_type(
        get_stylesheet_directory() . '/blocks/gallery-item'
    );
    register_block_type(
        get_stylesheet_directory() . '/blocks/artwork-list-item'
    );
});

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
    ]);
});

// Flush rewrite rules once after activation
add_action('after_switch_theme', function () {
    // Post type & taxonomy are registered via 'init'; flush after.
    flush_rewrite_rules();
});
