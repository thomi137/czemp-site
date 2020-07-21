<?php
/**
 * The Template for displaying all single posts.
 *
 * @package sparkling
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php

        $args = array( 'post_type' => 'portfolio_item');
        $the_query = new WP_Query( $args );
        if($the_query->have_posts())  :
            while ( $the_query->have_posts() ) :
                $the_query->the_post(); ?>

                <h2><?php the_title();?></h2>
                <?php the_content();?>
                <p><?php the_field('beschreibung');?></p>
                <p><?php the_field('preis');?></p>

            <?php endwhile; // end of the loop.
            else:
              __('Noch keine Werke erfasst');
         endif
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
