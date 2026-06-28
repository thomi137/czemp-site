<?php
/**
 * Title: Über mich – Zweispaltig
 * Slug: czemp/about-two-col
 * Categories: about
 * Description: Über-mich-Bereich mit Bild links und Text rechts.
 *
 * @package czemp
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:80px;padding-bottom:80px">

    <!-- wp:columns {"style":{"spacing":{"blockGap":"60px"}}} -->
    <div class="wp-block-columns">

        <!-- wp:column {"width":"40%"} -->
        <div class="wp-block-column" style="flex-basis:40%">
            <!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"0"}}} -->
            <figure class="wp-block-image size-large"><img alt="Claudia Zemp" style="border-radius:0;aspect-ratio:3/4;object-fit:cover"/></figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"12px","textTransform":"uppercase","letterSpacing":"0.2em"},"color":{"text":"#888"}}} -->
            <p style="font-size:12px;text-transform:uppercase;letter-spacing:0.2em;color:#888">Über mich</p>
            <!-- /wp:paragraph -->
            <!-- wp:heading {"level":2,"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--prompt)","fontWeight":"300","fontSize":"clamp(28px, 4vw, 44px)"}}} -->
            <h2 style="font-family:var(--wp--preset--font-family--prompt);font-weight:300;font-size:clamp(28px,4vw,44px)">Claudia Zemp</h2>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","lineHeight":"1.8"},"color":{"text":"#444"}}} -->
            <p style="font-size:16px;line-height:1.8;color:#444">In meiner Arbeit suche ich die Stille zwischen den Dingen – den Moment, bevor die Form zur Bedeutung wird. Farbe ist für mich kein Mittel der Beschreibung, sondern des Erlebens.</p>
            <!-- /wp:paragraph -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","lineHeight":"1.8"},"color":{"text":"#444"}}} -->
            <p style="font-size:16px;line-height:1.8;color:#444">Ich lebe und arbeite in Zürich. Meine Werke entstehen in Acryl, Öl und gemischten Techniken auf Leinwand und Papier.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->
