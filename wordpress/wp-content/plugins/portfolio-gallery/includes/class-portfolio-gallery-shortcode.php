<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Portfolio_Gallery_Shortcode {

	/**
	 * Portfolio_Gallery_Shortcode constructor.
	 */
	public function __construct() {
		add_shortcode( 'huge_it_portfolio', array( $this, 'run_shortcode' ) );
		add_action( 'admin_footer', array( $this, 'inline_popup_content' ) );
		add_action( 'media_buttons_context', array( $this, 'add_editor_media_button' ) );

	}

	/**
	 * Run the shortcode on front-end
	 *
	 * @param $attrs
	 *
	 * @return string
	 */
	public function run_shortcode( $attrs ) {
		$attrs = shortcode_atts( array(
			'id' => 'no huge_it portfolio',

		), $attrs );

		global $wpdb;
		$query = $wpdb->prepare("SELECT portfolio_list_effects_s FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id=%d", $attrs['id']);
		$portfolio_view = $wpdb->get_var($query);

		do_action( 'portfolio_gallery_shortcode_scripts', $attrs['id'], $portfolio_view );
		do_action( 'portfolio_gallery_localize_scripts', $attrs['id'] );

		return $this->init_frontend( $attrs['id'] );
	}

	/**
	 * Show published portfolios in frontend
	 *
	 * @param $id
	 *
	 * @return string
	 */
	protected function init_frontend( $id ) {
		global $wpdb;

		$query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_images WHERE portfolio_id = '%d' ORDER BY ordering ASC", $id );

		$images = $wpdb->get_results( $query );
		/***<title display>***/
		$title  = array();
		$number = 0;
		foreach ( $images as $key => $row ) {
			$imagesuploader = explode( ";", $row->image_url );
			array_pop( $imagesuploader );
			$count = count( $imagesuploader );
			for ( $i = 0; $i < $count; $i ++ ) {
				$pathinfo    = pathinfo( $imagesuploader[ $i ] );
				$filename    = $pathinfo["filename"];
				$filename    = strtolower( $filename );
				$query       = $wpdb->prepare( "SELECT post_title FROM " . $wpdb->prefix . "posts WHERE post_name = '%s'", $filename );
				$post_result = $wpdb->get_var( $query );
				$concat      = $post_result . "_-_-_" . $imagesuploader[ $i ];
				if ( in_array( $concat, $title ) ) {
					continue;
				}
				$title[ $number ] = $concat;
				$number ++;
			}
		}
		/***</title display>***/

		$query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id = '%d' ORDER BY id ASC", $id );

		$portfolio = $wpdb->get_results( $query );

		$paramssld = portfolio_gallery_get_general_options();

		ob_start();
		Portfolio_Gallery()->template_loader->load_front_end( $images, $paramssld, $portfolio, $title );

		return ob_get_clean();

	}

	/**
	 * Add editor media button
	 *
	 * @param $context
	 *
	 * @return string
	 */
	public function add_editor_media_button( $context ) {
		$img = untrailingslashit( Portfolio_Gallery()->plugin_url() ) . "/assets/images/admin_images/post.button.png";

		$container_id = 'huge_it_portfolio';

		$title = __( 'Select Huge IT Portfolio Gallery to insert into post', 'portfolio-gallery' );

		$button_text = __( 'Add Portfolio Gallery', 'portfolio-gallery' );

		$context .= '<a class="button thickbox" title="' . $title . '"    href="#TB_inline?width=400&inlineId=' . $container_id . '">
		<span class="wp-media-buttons-icon" style="background: url(' . $img . '); background-repeat: no-repeat; background-position: left bottom;"></span>' . $button_text . '</a>';

		return $context;
	}

	/**
	 * Inline popup contents
	 */
	public function inline_popup_content() {
		require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'portfolio-gallery-admin-inline-popup-content-html.php';
	}


}

new Portfolio_Gallery_Shortcode();