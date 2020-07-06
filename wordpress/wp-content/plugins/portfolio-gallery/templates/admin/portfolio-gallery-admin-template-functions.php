<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Get Options array 
 * 
 * @param string $op_type
 */
function portfolio_gallery_showStyles( $op_type = "0" ) {
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
 * Save General Option page options 
 */
function save_styles_options()
{
	global $wpdb;
	if (isset($_POST['params'])) {
		$params = $_POST['params'];
		foreach ($params as $key => $value) {
			$wpdb->update($wpdb->prefix . 'huge_itportfolio_params',
				array('value' => $value),
				array('name' => $key),
				array('%s')
			);
		}
		?>
		<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
		<?php
	}
	portfolio_gallery_showStyles();
}