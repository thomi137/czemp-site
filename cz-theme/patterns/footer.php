<?php
/**
 * Title: Footer
 * Slug: czemp/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * 
 * @author Thomas Prosser
 * @package czemp
 * @since 1.0
 */
?>

<!-- wp:group {"backgroundColor":"black","textColor":"white","style":{"spacing":{"padding":{"top":"50px","bottom":"40px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-white-color has-black-background-color has-text-color has-background" style="padding-top:50px;padding-bottom:40px">
    <!-- wp:site-logo /-->

    <!-- wp:site-title {"level":2,"textColor":"white","style":{"typography":{"textTransform":"uppercase"}}} /-->


    <!-- wp:navigation {"textColor":"white","overlayMenu":"never","style":{"typography":{"textTransform":"uppercase"},"spacing":{"blockGap":"24px"}},"layout":{"type":"flex","justifyContent":"center"}} -->
        <!-- wp:navigation-link {"label":"Blog","url":"/blog"} /-->
        <!-- wp:navigation-link {"label":"About","url":"/about"} /-->
        <!-- wp:navigation-link {"label":"Shop","url":"/shop"} /-->
    <!-- /wp:navigation -->

    <!-- wp:paragraph {"align":"center","textColor":"white"} -->
    <p class="has-text-align-center has-white-color">
        © <?php echo date('Y'); ?> Claudia Zemp
    </p>
    <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
