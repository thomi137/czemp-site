<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Portfolio_Gallery_Admin {

	/**
	 * Array of pages in admin
	 * @var array
	 */
	public $pages = array();

	/**
	 * Instance of Portfolio_Gallery_General_Options class
	 *
	 * @var Portfolio_Gallery_General_Options
	 */
	public $general_options = null;

	/**
	 * Instance of Portfolio_Gallery_Portfolios class
	 * 
	 * @var Portfolio_Gallery_Portfolios
	 */
	public $portfolios = null;

	/**
	 * Instance of Portfolio_Gallery_Lightbox_Options class
	 *
	 * @var Portfolio_Gallery_Lightbox_Options
	 */
	public $lightbox_options = null;
	
	/**
	 * Instance of Portfolio_Gallery_Featured_Plugins class
	 *
	 * @var Portfolio_Gallery_Featured_Plugins
	 */
	public $featured_plugins = null;

	/**
	 * Portfolio_Gallery_Admin constructor.
	 */
	public function __construct(){
		$this->init();
		add_action('admin_menu',array($this,'admin_menu'));
		add_action( 'wp_loaded', array($this,'wp_loaded') );
	}

	/**
	 * Initialize Portfolio Gallery's admin
	 */
	protected function init(){
		$this->general_options = new Portfolio_Gallery_General_Options();
		$this->portfolios = new Portfolio_Gallery_Portfolios();
		$this->lightbox_options = new Portfolio_Gallery_Lightbox_Options();
		$this->featured_plugins = new Portfolio_Gallery_Featured_Plugins();
	}
		
	/**
	 * Prints Portfolio Menu
	 */
	public function admin_menu(){
		$this->pages[] = add_menu_page( __( 'Huge-IT Portfolio Gallery', 'portfolio-gallery' ),  __( 'Huge-IT Portfolio', 'portfolio-gallery' ), 'delete_pages', 'portfolios_huge_it_portfolio', array( Portfolio_Gallery()->admin->portfolios,'load_portfolio_page' ), PORTFOLIO_GALLERY_IMAGES_URL."/admin_images/huge_it_portfolioLogoHover -for_menu.png" );
		$this->pages[] = add_submenu_page( 'portfolios_huge_it_portfolio', __('Portfolios','portfolio-gallery'), __('Portfolios','portfolio-gallery'), 'delete_pages', 'portfolios_huge_it_portfolio', array( Portfolio_Gallery()->admin->portfolios,'load_portfolio_page' ));

		$this->pages[] = add_submenu_page( 'portfolios_huge_it_portfolio', __( 'General Options', 'portfolio-gallery' ), __( 'General Options', 'portfolio-gallery' ), 'delete_pages', 'Options_portfolio_styles', array( Portfolio_Gallery()->admin->general_options ,'load_page' ) );
		$this->pages[] = add_submenu_page( 'portfolios_huge_it_portfolio', __( 'Lightbox Options', 'portfolio-gallery' ), __( 'Lightbox Options', 'portfolio-gallery' ), 'delete_pages', 'Options_portfolio_lightbox_styles', array( Portfolio_Gallery()->admin->lightbox_options,'load_page' ) );

		$this->pages[] =add_submenu_page( 'portfolios_huge_it_portfolio', __( 'Featured Plugins', 'portfolio-gallery' ), __( 'Featured Plugins', 'portfolio-gallery' ), 'delete_pages', 'huge_it__portfolio_featured_plugins', array( Portfolio_Gallery()->admin->featured_plugins,'show_page' ) );
	}


	public function wp_loaded (){
		global $wpdb;
		if(isset($_GET['task'])){
			$task = $_GET['task'];
			if($task == 'add_portfolio'){
				$table_name = $wpdb->prefix . "huge_itportfolio_portfolios";
				$sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `sl_height`, `sl_width`, `pause_on_hover`, `portfolio_list_effects_s`, `description`, `param`, `sl_position`, `ordering`, `published`, `categories`, `ht_show_sorting`, `ht_show_filtering`) VALUES
( 'New portfolio', '375', '600', 'on', 'cubeH', '4000', '1000', 'off', '1', '300', 'My First Category,My Second Category,My Third Category,', 'off', 'off')";

				$wpdb->query($sql_2);

				$query="SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios order by id ASC";
				$rowsldcc=$wpdb->get_results($query);
				$last_key = key( array_slice( $rowsldcc, -1, 1, TRUE ) );


				foreach($rowsldcc as $key=>$rowsldccs){
					if($last_key == $key){
						$portfolio_wp_nonce = wp_create_nonce('huge_it_portfolio_nonce');
						$wp_nonce = $_GET['huge_it_portfolio_nonce'];
						if(!wp_verify_nonce($wp_nonce, 'huge_it_portfolio_nonce') && !wp_verify_nonce($wp_nonce, 'huge_it_portfolio_nonce')) {
							wp_die('Security check fail');
						}
						header('Location: admin.php?page=portfolios_huge_it_portfolio&id='.$rowsldccs->id.'&task=apply&huge_it_portfolio_nonce='.$portfolio_wp_nonce);
					}
				}
			}
		}
	}

}

