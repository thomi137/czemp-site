<?php
/**
 * Plugin Name: Gallery Plugin
 * Description: Site specific portfolio post type for claudia-zemp.ch
 * Author: thomIT GmbH
 * Author URI: www.thomit.com
 */

/* Start Adding Functions Below this Line */

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
