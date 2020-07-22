<?php
if ( ! function_exists( 'cz_header_menu' ) ) :
  /**
   * Header menu (should you choose to use one)
   */
  function cz_header_menu() {
    // display the WordPress Custom Menu if available
    wp_nav_menu(
      array(
        'menu'            => 'primary',
        'theme_location'  => 'primary',
        'container'       => 'div',
        'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
        'menu_class'      => 'nav navbar-nav',
        'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
        'walker'          => new WP_Bootstrap_Navwalker(),
      )
    );
  } /* end header menu */
endif;