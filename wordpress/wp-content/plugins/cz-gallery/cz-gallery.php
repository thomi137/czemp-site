<?php
/**
 * Plugin Name: Gallery Plugin
 * Description: Site specific portfolio post type for claudia-zemp.ch
 * Author: thomIT GmbH
 * Author URI: www.thomit.com
 */

/* Start Adding Functions Below this Line */

/**
 * Remove old Sparkling Theme Bootstrap
 */
function cz_remove_old_bootstrap(){
  wp_dequeue_style('sparkling-bootstrap');
  wp_dequeue_script('sparkling-bootstrapjs');
}
add_action('wp_print_styles', 'cz_remove_old_bootstrap');

/**
 * Add shiny new Bootstrap
 */
function cz_add_new_bootstrap(){
  wp_enqueue_style('cz_bootstrap_version', plugin_dir_url(__FILE__).'css/bootstrap.min.css');
  wp_enqueue_script('cz_bootstrap_scripts', plugin_dir_url(__FILE__).'js/bootstrap.bundle.min.js');
}
add_action('wp_enqueue_scripts', 'cz_add_new_bootstrap');

function create_posttype() {

  register_post_type( 'portfolio_item',

    // CPT Options
    array(
      'labels' => array(
        'name' => __( 'Werke' ),
        'singular_name' => __( 'Werk' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'portfolio_item'),
      'show_in_rest' => true,
    )
  );

}

add_action('init', 'create_posttype');
/* Stop Adding Functions Below this Line */
?>
