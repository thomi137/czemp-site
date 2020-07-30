<?php

function cz_add_main_scripts() {
    wp_enqueue_script( 'cz_boostrap_js', get_stylesheet_directory_uri() . '/dist/bootstrap.bundle.min.js', array('jquery'), false, true);
    wp_enqueue_script( 'cz_main_script', get_stylesheet_directory_uri() . '/dist/main.js', array('jquery'), false, true );
}
add_action( 'wp_enqueue_scripts', 'cz_add_main_scripts' );

/**
 * Register Custom Navigation Walker
 */
function cz_register_navwalker(){
  if ( ! file_exists( get_stylesheet_directory_uri() . '/class-wp-bootstrap-navwalker.php' ) ) {
    // File does not exist... return an error.
    return new WP_Error( 'class-wp-bootstrap-navwalker-missing', __( 'It appears the class-wp-bootstrap-navwalker.php file may be missing.', 'wp-bootstrap-navwalker' ) );
  } else {
    // File exists... require it.
    require_once get_stylesheet_directory_uri() . '/class-wp-bootstrap-navwalker.php';
  }
}
add_action( 'after_setup_theme', 'cz_register_navwalker' );