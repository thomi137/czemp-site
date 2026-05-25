<?php
/**
 * Claudia Zemp functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * 
 * @author Thomas Prosser
 *
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


add_action('init', function () {

	register_block_type(
		get_template_directory() . '/blocks/gallery-item'
	);

	register_block_type(
		get_template_directory() . '/blocks/artwork-list-item'
	);

});

add_action('init', function() {

	register_post_type('artwork', [
		'labels' => [
			'name' => 'Artworks',
			'singular_name' => 'Artwork',
		],
		'public' => true,
		'show_in_rest' => true,
		'supports' => [
			'title',
			'editor',
			'thumbnail',
			'excerpt'
		],
		'has_archive' => true,
		'rewrite' => [
			'slug' => 'artworks'
		],
	]);

});
