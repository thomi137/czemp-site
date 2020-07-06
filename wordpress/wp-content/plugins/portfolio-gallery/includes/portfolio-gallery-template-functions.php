<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function portfolio_gallery_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	$located = portfolio_gallery_locate_template( $template_name, $template_path, $default_path );
	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ) );

		return;
	}
	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'portfolio_gallery_get_template', $located, $template_name, $args, $template_path, $default_path );
	do_action( 'portfolio_gallery_before_template_part', $template_name, $template_path, $located, $args );
	include( $located );
	do_action( 'portfolio_gallery_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        yourtheme        /    $template_path    /    $template_name
 *        yourtheme        /    $template_name
 *        $default_path    /    $template_name
 *
 *
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 *
 * @return string
 */


function portfolio_gallery_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = Portfolio_Gallery()->template_path();
	}
	if ( ! $default_path ) {
		$default_path = Portfolio_Gallery()->plugin_path() . '/templates/';
	}
	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);
	// Get default template/
	if ( ! $template || PORTFOLIO_GALLERY_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'portfolio_gallery_locate_template', $template, $template_name, $template_path );
}
