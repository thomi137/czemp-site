<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Portfolio_Gallery_General_Options {
	
	public function __construct() {
		add_action( 'portfolio_gallery_save_general_options', array($this,'save_options') );
	}

	/**
	 * Loads General options page
	 */
	public function load_page(){
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'Options_portfolio_styles' ) {
			if ( isset( $_GET['task'] ) ) {
				if ( $_GET['task'] == 'save' ) {
					do_action( 'portfolio_gallery_save_general_options' );
				}
			} else {
				$this->show_page();
			}
		}
	}

	/**
	 * Shows General options page
	 */
	public function show_page(){
		global $wpdb;
		$query        = "SELECT *  from " . $wpdb->prefix . "huge_itportfolio_params ";
		$rows         = $wpdb->get_results( $query );
		$param_values = array();
		foreach ( $rows as $row ) {
			$key                  = $row->name;
			$value                = $row->value;
			$param_values[ $key ] = $value;
		}
		require( PORTFOLIO_GALLERY_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'portfolio-gallery-admin-general-options-html.php' );
	}

	/**
	 * Save General Options
	 * //@todo: get rid of foreach
	 */
	public function save_options(){
		global $wpdb;
		if (isset($_POST['params'])) {
			$params = $_POST['params'];
			foreach ($params as $key => $value) {
				$wpdb->update($wpdb->prefix . 'huge_itportfolio_params',
					array('value' => wp_unslash($value)),
					array('name' => $key),
					array('%s')
				);
			}
			?>
			<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
			<?php
		}
		$this->show_page();
	}
}