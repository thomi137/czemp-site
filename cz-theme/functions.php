<?php
/**
 * Claudia Zemp functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
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

add_action('init', function() {

	register_block_type( get_stylesheet_directory() . '/blocks/gallery-item' );

	// Load block editor JS
	wp_register_script(
		'czemp-blocks',
		get_stylesheet_directory_uri() . '/build/index.js',
		[
			'wp-blocks',
			'wp-element',
			'wp-block-editor',
			'wp-components'
		],
		filemtime(get_stylesheet_directory() . '/build/index.js')
	);


	wp_register_style(
		'czemp-style',
		get_stylesheet_uri()
	);

});