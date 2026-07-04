<?php
/**
 * Title: Header
 * Slug: czemp/header
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Site header with logo and site title.
 *
 * @author Thomas Prosser
 * @package czemp
 * @since 1.0
 */
?>

<!-- wp:group {"tagName":"header","className":"cz-header","layout":{"type":"constrained"}} -->
<header class="wp-block-group cz-header">

    <!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center","justifyContent":"left"},"style":{"spacing":{"blockGap":"16px"}}} -->
    <div class="wp-block-group" style="gap:16px">

        <!-- wp:image {"url":"\/wp-content\/themes\/cz-theme\/assets\/images\/cz_Logo_without_text.webp","alt":"Claudia Zemp","width":72,"sizeSlug":"full","linkDestination":"custom","href":"/"} -->
        <figure class="wp-block-image size-full">
            <a href="/"><img src="/wp-content/themes/cz-theme/assets/images/cz_Logo_without_text.webp" alt="Claudia Zemp" width="72"/></a>
        </figure>
        <!-- /wp:image -->

        <!-- wp:site-title {"style":{"typography":{"fontWeight":"300","letterSpacing":"0.08em","textTransform":"uppercase"},"elements":{"link":{"color":{"text":"#000000"},":hover":{"color":{"text":"#000000"}}}}}} /-->

    </div>
    <!-- /wp:group -->

</header>
<!-- /wp:group -->
