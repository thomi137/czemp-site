<?php
/**
 * Title: Footer
 * Slug: czemp/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 *
 * @package czemp
 */
?>
<!-- wp:czemp-theme/sticky-nav -->
<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"blockGap":"32px"},"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontSize":"18px"}},"textColor":"white"} /-->
<!-- /wp:czemp-theme/sticky-nav -->

<!-- wp:group {"tagName":"footer","style":{"spacing":{"padding":{"top":"16px","bottom":"16px"}},"color":{"background":"#000000","text":"#ffffff"}},"layout":{"type":"flex","justifyContent":"center"}} -->
<footer class="wp-block-group has-white-color has-black-background-color has-text-color has-background" style="padding-top:16px;padding-bottom:16px">
    <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"12px"},"color":{"text":"#ffffff"}}} -->
    <p class="has-text-align-center" style="font-size:12px;color:#fff">© <?php echo date('Y'); ?> Claudia Zemp</p>
    <!-- /wp:paragraph -->
</footer>
<!-- /wp:group -->
