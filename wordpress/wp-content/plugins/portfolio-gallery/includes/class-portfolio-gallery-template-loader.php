<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Portfolio_Gallery_Template_Loader {

	public function __contstruct(){
		add_action('media_buttons_context', array($this,'add_editor_media_button'));
	}

	

	/**
	 * Load the Plugin shortcode's frontend
	 *
	 * @param $images
	 * @param $paramssld
	 * @param $portfolio
	 * @param $title
	 */
	public function load_front_end( $images, $paramssld, $portfolio, $title ){
		global $portfolio_gallery_images_natural_size;

		$portfolioID             = $portfolio[0]->id;
		$portfoliotitle          = $portfolio[0]->name;
		$portfolioheight         = $portfolio[0]->sl_height;
		$portfoliowidth          = $portfolio[0]->sl_width;
		$portfolioeffect         = $portfolio[0]->portfolio_list_effects_s;
		$slidepausetime          = ( $portfolio[0]->description + $portfolio[0]->param );
		$portfoliopauseonhover   = $portfolio[0]->pause_on_hover;
		$portfolioposition       = $portfolio[0]->sl_position;
		$slidechangespeed        = $portfolio[0]->param;
		$portfolioCats           = $portfolio[0]->categories;
		$portfolioShowSorting    = $portfolio[0]->ht_show_sorting;
		$portfolioShowFiltering  = $portfolio[0]->ht_show_filtering;
		$portfolioShowLoading    = $portfolio[0]->show_loading;
		$portfolioLoadingIconype = $portfolio[0]->loading_icon_type;
		$image_prefix            = "_huge_it_small_portfolio";


		
		$view =  $portfolioeffect;
		switch ( $view ){
			case 0:
				if ( $portfolioShowSorting == 'on' ) {
					$sortingFloatToggle = $paramssld["ht_view0_sorting_float"];
				} else {
					$sortingFloatToggle = '';
				}
				if ( $portfolioShowFiltering == 'on' ) {
					$filteringFloatToggle = $paramssld["ht_view0_filtering_float"];
				} else {
					$filteringFloatToggle = '';
				}
				$view_slug = portfolio_gallery_get_view_slag_by_id( $portfolioID );
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'toggle-up-down'.DIRECTORY_SEPARATOR.'toggle_up_down-view.php';
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'toggle-up-down'.DIRECTORY_SEPARATOR.'toggle_up_down-view.css.php';
				break;
			case 1:
				if ( $portfolioShowSorting == 'on' ) {
					$sortingFloatFullHeight = $paramssld["ht_view1_sorting_float"];
				} else {
					$sortingFloatFullHeight = '';
				}
				if ( $portfolioShowFiltering == 'on' ) {
					$filteringFloatFullHeight = $paramssld["ht_view1_filtering_float"];
				} else {
					$filteringFloatFullHeight = '';
				}
				$view_slug = portfolio_gallery_get_view_slag_by_id( $portfolioID );
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'full-height'.DIRECTORY_SEPARATOR.'full-height-view.php';
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'full-height'.DIRECTORY_SEPARATOR.'full-height-view.css.php';
				break;
			case 2:
				if ( $portfolioShowSorting == 'on' ) {
					$sortingFloatPopup = $paramssld["ht_view2_sorting_float"];
				} else {
					$sortingFloatPopup = '';
				}
				if ( $portfolioShowFiltering == 'on' ) {
					$filteringFloatPopup = $paramssld["ht_view2_filtering_float"];
				} else {
					$filteringFloatPopup = '';
				}
				$view_slug = portfolio_gallery_get_view_slag_by_id( $portfolioID );
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'content-popup'.DIRECTORY_SEPARATOR.'content-popup-view.php';
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'content-popup'.DIRECTORY_SEPARATOR.'content-popup-view.css.php';
				break;
			case 3:
				if ( $portfolioShowSorting == 'on' ) {
					$sortingFloatFullWidth = $paramssld["ht_view3_sorting_float"];
				} else {
					$sortingFloatFullWidth = '';
				}
				if ( $portfolioShowFiltering == 'on' ) {
					$filteringFloatFullWidth = $paramssld["ht_view3_filtering_float"];
				} else {
					$filteringFloatFullWidth = '';
				}
				$view_slug = portfolio_gallery_get_view_slag_by_id( $portfolioID );
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'full-width'.DIRECTORY_SEPARATOR.'full-width-view.php';
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'full-width'.DIRECTORY_SEPARATOR.'full-width-view.css.php';
				break;
			case 4:
				if ( $portfolioShowSorting == 'on' ) {
					$sortingFloatFaq = $paramssld["ht_view4_sorting_float"];
				} else {
					$sortingFloatFaq = '';
				}
				if ( $portfolioShowFiltering == 'on' ) {
					$filteringFloatFaq = $paramssld["ht_view4_filtering_float"];
				} else {
					$filteringFloatFaq = '';
				}
				$view_slug = portfolio_gallery_get_view_slag_by_id( $portfolioID );
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'faq'.DIRECTORY_SEPARATOR.'faq-view.php';
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'faq'.DIRECTORY_SEPARATOR.'faq-view.css.php';
				break;
			case 5:
				$view_slug = portfolio_gallery_get_view_slag_by_id( $portfolioID );
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'content-slider'.DIRECTORY_SEPARATOR.'content-slider-view.php';
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'content-slider'.DIRECTORY_SEPARATOR.'content-slider-view.css.php';
				break;
			case 6:
				if ( $portfolioShowSorting == 'on' ) {
					$sortingFloatLgal = $paramssld["ht_view6_sorting_float"];
				} else {
					$sortingFloatLgal = '';
				}
				if ( $portfolioShowFiltering == 'on' ) {
					$filteringFloatLgal = $paramssld["ht_view6_filtering_float"];
				} else {
					$filteringFloatLgal = '';
				}
				$view_slug = portfolio_gallery_get_view_slag_by_id( $portfolioID );
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'lightbox-gallery'.DIRECTORY_SEPARATOR.'lightbox-gallery-view.php';
				require PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'lightbox-gallery'.DIRECTORY_SEPARATOR.'lightbox-gallery-view.css.php';
				break;
		}
		

	}
}