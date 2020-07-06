<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Class Portfolio_Gallery
 */
class Portfolio_Gallery_Frontend_Scripts {

	/**
	 * Portfolio_Gallery constructor.
	 */
	public function __construct() {
		add_action( 'portfolio_gallery_shortcode_scripts', array( $this, 'frontend_scripts' ), 10, 2 );
		add_action( 'portfolio_gallery_shortcode_scripts', array( $this, 'frontend_styles' ), 10, 2 );
		add_action( 'portfolio_gallery_localize_scripts', array( $this, 'localize_scripts' ), 10, 1 );
	}

	/**
	 * Enqueue styles
	 */
	public function frontend_styles( $id, $portfolio_view ) {
		$general_options = portfolio_gallery_get_general_options();

		wp_register_style( 'portfolio-all-css', plugins_url( '../assets/style/portfolio-all.css', __FILE__ ) );
		wp_enqueue_style( 'portfolio-all-css' );

		wp_register_style( 'style2-os-css', plugins_url( '../assets/style/style2-os.css', __FILE__ ) );
		wp_enqueue_style( 'style2-os-css' );

		wp_register_style( 'lightbox-css', plugins_url( '../assets/style/lightbox.css', __FILE__ ) );
		wp_enqueue_style( 'lightbox-css' );

		wp_enqueue_style( 'portfolio_gallery_colorbox_css', untrailingslashit( Portfolio_Gallery()->plugin_url() ) . '/assets/style/colorbox-' . $general_options['light_box_style'] . '.css' );

		if($portfolio_view == '5'){
			wp_register_style( 'animate-css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.0.0/animate.min.css' );
			wp_enqueue_style( 'animate-css' );
			wp_register_style( 'liquid-slider-css', plugins_url( '../assets/style/liquid-slider.css', __FILE__ ) );
			wp_enqueue_style( 'liquid-slider-css' );
		}
	}

	/**
	 * Enqueue scripts
	 */
	public function frontend_scripts( $id, $portfolio_view ) {
		$view_slug = portfolio_gallery_get_view_slag_by_id( $id );

		if ( ! wp_script_is( 'jquery' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		wp_register_script( 'jquery.pcolorbox-js', plugins_url( '../assets/js/jquery.colorbox.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'jquery.pcolorbox-js' );

		wp_register_script( 'hugeitmicro-min-js', plugins_url( '../assets/js/jquery.hugeitmicro.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'hugeitmicro-min-js' );

		wp_register_script( 'front-end-js', plugins_url( '../assets/js/view-' . $view_slug . '.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'front-end-js' );

		wp_register_script( 'custom-js', plugins_url( '../assets/js/custom.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'custom-js' );

		if($portfolio_view == '5'){
			wp_register_script( 'easing-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'easing-js' );
			wp_register_script( 'touch_swipe-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.4/jquery.touchSwipe.min.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'touch_swipe-js' );
			wp_register_script( 'liquid-slider-js', plugins_url( '../assets/js/jquery.liquid-slider.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'liquid-slider-js' );
		}
	}

	public function localize_scripts( $id ) {
		global $wpdb;
		$query           = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id=%d", $id );
		$portfolio       = $wpdb->get_results( $query );
		$portfolio_param = portfolio_gallery_get_general_options();

		$portfolioID             = $portfolio[0]->id;
		$portfoliotitle          = $portfolio[0]->name;
		$portfolioheight         = $portfolio[0]->sl_height;
		$portfoliowidth          = $portfolio[0]->sl_width;
		$portfolio_view          = $portfolio[0]->portfolio_list_effects_s;
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

		$float                  = array();
		$float['isCenter']      = $portfolioposition;
		$float['showFiltering'] = $portfolioShowFiltering;
		$float['showSorting']   = $portfolioShowSorting;

		switch ( $portfolio_view ) {
			case 0:
				if ( $portfolioShowSorting == 'on' ) {
					$float['sorting'] = $portfolio_param["ht_view0_sorting_float"];
				} else {
					$float['sorting'] = '';
				}
				if ( $portfolioShowFiltering == 'on' ) {
					$float['filtering'] = $portfolio_param["ht_view0_filtering_float"];
				} else {
					$float['filtering'] = '';
				}

				if ( $portfolio_param["port_natural_size_contentpopup"] == 'resize' ) {
					$portfolio_gallery_images_natural_size = false;
				} elseif ( $portfolio_param["port_natural_size_contentpopup"] == 'natural' ) {
					$portfolio_gallery_images_natural_size = true;
				}
				break;
		}
		$lightbox = array(
			'lightbox_transition'     => $portfolio_param['light_box_transition'],
			'lightbox_speed'          => $portfolio_param['light_box_speed'],
			'lightbox_fadeOut'        => $portfolio_param['light_box_fadeout'],
			'lightbox_title'          => $portfolio_param['light_box_title'],
			'lightbox_scalePhotos'    => $portfolio_param['light_box_scalephotos'],
			'lightbox_scrolling'      => $portfolio_param['light_box_scrolling'],
			'lightbox_opacity'        => ( $portfolio_param['light_box_opacity'] / 100 ) + 0.001,
			'lightbox_open'           => $portfolio_param['light_box_open'],
			'lightbox_returnFocus'    => $portfolio_param['light_box_returnfocus'],
			'lightbox_trapFocus'      => $portfolio_param['light_box_trapfocus'],
			'lightbox_fastIframe'     => $portfolio_param['light_box_fastiframe'],
			'lightbox_preloading'     => $portfolio_param['light_box_preloading'],
			'lightbox_overlayClose'   => $portfolio_param['light_box_overlayclose'],
			'lightbox_escKey'         => $portfolio_param['light_box_esckey'],
			'lightbox_arrowKey'       => $portfolio_param['light_box_arrowkey'],
			'lightbox_loop'           => $portfolio_param['light_box_loop'],
			'lightbox_closeButton'    => $portfolio_param['light_box_closebutton'],
			'lightbox_previous'       => $portfolio_param['light_box_previous'],
			'lightbox_next'           => $portfolio_param['light_box_next'],
			'lightbox_close'          => $portfolio_param['light_box_close'],
			'lightbox_html'           => $portfolio_param['light_box_html'],
			'lightbox_photo'          => $portfolio_param['light_box_photo'],
			'lightbox_innerWidth'     => $portfolio_param['light_box_innerwidth'],
			'lightbox_innerHeight'    => $portfolio_param['light_box_innerheight'],
			'lightbox_initialWidth'   => $portfolio_param['light_box_initialwidth'],
			'lightbox_initialHeight'  => $portfolio_param['light_box_initialheight'],
			'lightbox_slideshow'      => $portfolio_param['light_box_slideshow'],
			'lightbox_slideshowSpeed' => $portfolio_param['light_box_slideshowspeed'],
			'lightbox_slideshowAuto'  => $portfolio_param['light_box_slideshowauto'],
			'lightbox_slideshowStart' => $portfolio_param['light_box_slideshowstart'],
			'lightbox_slideshowStop'  => $portfolio_param['light_box_slideshowstop'],
			'lightbox_fixed'          => $portfolio_param['light_box_fixed'],
			'lightbox_reposition'     => $portfolio_param['light_box_reposition'],
			'lightbox_retinaImage'    => $portfolio_param['light_box_retinaimage'],
			'lightbox_retinaUrl'      => $portfolio_param['light_box_retinaurl'],
			'lightbox_retinaSuffix'   => $portfolio_param['light_box_retinasuffix'],
			'lightbox_maxWidth'       => $portfolio_param['light_box_maxwidth'],
			'lightbox_maxHeight'      => $portfolio_param['light_box_maxheight'],
			'lightbox_sizeFix'        => $portfolio_param['light_box_size_fix']
		);

		if ( $portfolio_param['light_box_size_fix'] == 'false' ) {
			$lightbox['lightbox_width'] = '';
		} else {
			$lightbox['lightbox_width'] = $portfolio_param['light_box_width'];
		}

		if ( $portfolio_param['light_box_size_fix'] == 'false' ) {
			$lightbox['lightbox_height'] = '';
		} else {
			$lightbox['lightbox_height'] = $portfolio_param['light_box_height'];
		}

		$pos = $portfolio_param['slider_title_position'];
		switch ( $pos ) {
			case 1:
				$lightbox['lightbox_top']    = '10%';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = '10%';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 2:
				$lightbox['lightbox_top']    = '10%';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 3:
				$lightbox['lightbox_top']    = '10%';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = '10%';
				break;
			case 4:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = '10%';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 5:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 6:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = '10%';
				break;
			case 7:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = '10%';
				$lightbox['lightbox_left']   = '10%';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 8:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = '10%';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 9:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = '10%';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = '10%';
				break;
		}


		wp_localize_script( 'front-end-js', 'param_obj', $portfolio_param );
		wp_localize_script( 'front-end-js', 'portfolio_obj', $portfolio );
		wp_localize_script( 'front-end-js', 'float', $float );
		wp_localize_script( 'jquery.pcolorbox-js', 'lightbox_obj', $lightbox );
		wp_localize_script( 'custom-js', 'portfolioId', $id ); ?>

		<?php
	}
}

new Portfolio_Gallery_Frontend_Scripts();