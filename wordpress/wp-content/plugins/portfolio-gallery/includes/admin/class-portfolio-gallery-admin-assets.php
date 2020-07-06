<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//todo: correct urls
class Portfolio_Gallery_Admin_Assets {

	/**
	 * Portfolio_Gallery_Admin_Assets constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * @param $hook hook of current page
	 */
	public function admin_styles( $hook ){
		if( in_array($hook, Portfolio_Gallery()->admin->pages ) ){
			wp_enqueue_style( "admin_css", Portfolio_Gallery()->plugin_url()."/assets/style/admin.style.css", false );
			wp_enqueue_style( "jquery_ui", esc_url("http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css"), false );
			wp_enqueue_style( "simple_slider_css", Portfolio_Gallery()->plugin_url()."/assets/style/simple-slider.css",  false );
			wp_enqueue_style( "featured_plugins", Portfolio_Gallery()->plugin_url()."/assets/style/featured-plugins.css",  false );
		}
	}

	public function admin_scripts( $hook ) {
		if( in_array($hook, Portfolio_Gallery()->admin->pages ) ){
			wp_enqueue_media();
			wp_enqueue_script( "admin_js", Portfolio_Gallery()->plugin_url()."/assets/js/admin.js", false );
			wp_enqueue_script( "jquery_ui_new", esc_url("http://code.jquery.com/ui/1.10.4/jquery-ui.js"), false );
			wp_enqueue_script( "simple_slider_js", Portfolio_Gallery()->plugin_url().'/assets/js/simple-slider.js', false );
			wp_enqueue_script( 'param_block2', Portfolio_Gallery()->plugin_url()."/assets/js/jscolor.js");
		}
	}

	public function localize_scripts(){

	}
}

new Portfolio_Gallery_Admin_Assets();