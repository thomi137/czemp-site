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

<!-- wp:group {"style":{"position":{"type":"sticky"},"spacing":{"padding":{"top":"16px","bottom":"16px"},"margin":{"bottom":"0"}},"color":{"background":"#000000"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-black-background-color has-background" style="position:sticky;bottom:0;padding-top:16px;padding-bottom:16px;margin-bottom:0">
    <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"32px"},"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontSize":"18px"}},"textColor":"white"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"footer","style":{"spacing":{"padding":{"top":"40px","bottom":"24px"},"margin":{"top":"0"}},"color":{"background":"#000000","text":"#ffffff"}},"layout":{"type":"constrained"}} -->
<footer class="wp-block-group has-white-color has-black-background-color has-text-color has-background" style="padding-top:40px;padding-bottom:24px;margin-top:0">

    <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"12px"},"color":{"text":"#666"}}} -->
    <p class="has-text-align-center" style="font-size:12px;color:#666">© <?php echo date('Y'); ?> Claudia Zemp</p>
    <!-- /wp:paragraph -->

</footer>
<!-- /wp:group -->
