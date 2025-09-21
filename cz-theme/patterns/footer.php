<?php
/**
 * Title: Footer
 * Slug: czemp/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Site footer with site title, navigation and logo.
 *
 * @package czewmp
 * @since 1.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
    <div class="wp-block-group alignwide"><!-- wp:group {"layout":{"type":"grid","minimumColumnWidth":null,"columnCount":6}} -->
        <div class="wp-block-group"><!-- wp:group {"style":{"layout":{"columnSpan":3,"rowSpan":1}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
            <div class="wp-block-group"><!-- wp:group {"style":{"layout":{"columnSpan":3,"rowSpan":1}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                <div class="wp-block-group"><!-- wp:site-logo /-->

                    <!-- wp:site-title {"level":2,"style":{"layout":{"columnSpan":2,"rowSpan":1}}} /--></div>
                <!-- /wp:group --></div>
            <!-- /wp:group -->

            <!-- wp:spacer -->
            <div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
            <!-- /wp:spacer -->

            <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|80"},"layout":{"columnSpan":2,"rowSpan":1}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"top","justifyContent":"right"}} -->
            <div class="wp-block-group"><!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} -->
                <!-- wp:navigation-link {"label":"Blog","url":"#"} /-->

                <!-- wp:navigation-link {"label":"About","url":"#"} /-->

                <!-- wp:navigation-link {"label":"FAQs","url":"#"} /-->

                <!-- wp:navigation-link {"label":"Authors","url":"#"} /-->
                <!-- /wp:navigation -->

                <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} -->
                <!-- wp:navigation-link {"label":"Events","url":"#"} /-->

                <!-- wp:navigation-link {"label":"Shop","url":"#"} /-->

                <!-- wp:navigation-link {"label":"Patterns","url":"#"} /-->

                <!-- wp:navigation-link {"label":"Themes","url":"#"} /-->
                <!-- /wp:navigation --></div>
            <!-- /wp:group --></div>
        <!-- /wp:group -->

        <!-- wp:spacer {"height":"var:preset|spacing|40"} -->
        <div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
        <!-- /wp:spacer -->

        <!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
        <div class="wp-block-group alignfull"><!-- wp:paragraph {"className":"has-medium-font-size","fontSize":"medium"} -->
            <p class="has-medium-font-size">
                &copy; <?php echo date('Y') ?> Claudia Zemp</p>
            <!-- /wp:paragraph --></div>
        <!-- /wp:group --></div>
    <!-- /wp:group --></div>
<!-- /wp:group -->