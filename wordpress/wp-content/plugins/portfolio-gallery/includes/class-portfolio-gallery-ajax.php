<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Portfolio_Gallery_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_portfolio_gallery_action', array($this,'callback') );
		add_action( 'wp_ajax_nopriv_portfolio_gallery_action', array($this,'callback') );
	}

	public function callback(){
		global $wpdb;
		if ( $_POST['post'] == 'portfolioChangeOptions' ) {
			if ( isset( $_POST['id'] ) ) {
				$id       = $_POST['id'];
				$query    = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itportfolio_portfolios WHERE id = %d", $id );
				$row      = $wpdb->get_row( $query );
				$response = array(
					'portfolio_effects_list' => $row->portfolio_list_effects_s,
					'ht_show_sorting'        => $row->ht_show_sorting,
					'ht_show_filtering'      => $row->ht_show_filtering,
					'sl_pausetime'           => $row->description,
					'sl_changespeed'         => $row->param,
					'pause_on_hover'         => $row->pause_on_hover
				);
				//$response = array('portfolio_effects_list' =>'$row->portfolio_list_effects_s');
				echo json_encode( $response );
				die();
			}
		}
		/**************************Options DB update****************************************/
		if ( $_POST['post'] == 'portfolioSaveOptions' ) {
			if ( isset( $_POST["htportfolio_id"] ) ) {
				$id = $_POST["htportfolio_id"];
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  ht_show_sorting = '%s'  WHERE id = %d ", sanitize_text_field( $_POST["ht_show_sorting"] ), $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  ht_show_filtering = '%s'  WHERE id = %d ", sanitize_text_field( $_POST["ht_show_filtering"] ), $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  description = '%s'  WHERE id = %d ", sanitize_text_field( $_POST["sl_pausetime"] ), $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  param = '%s'  WHERE id = %d ", sanitize_text_field( $_POST["sl_changespeed"] ), $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  description = '%s'  WHERE id = %d ", sanitize_text_field( $_POST["sl_pausetime"] ), $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  pause_on_hover = '%s'  WHERE id = %d ", sanitize_text_field( $_POST["pause_on_hover"] ), $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itportfolio_portfolios SET  portfolio_list_effects_s = '%s'  WHERE id = %d ", sanitize_text_field( $_POST["portfolio_effects_list"] ), $id ) );
				/*$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  sl_loading_icon = '%s' WHERE id = %d ", $_POST["sl_loading_icon"], $id));
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  show_thumb = '%s' WHERE id = %d ", $_POST["show_thumb"], $id));/*add*/
			}
		}
	}
}

new Portfolio_Gallery_Ajax();