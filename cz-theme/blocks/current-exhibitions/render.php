<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

$heading = isset($attributes['heading']) && '' !== $attributes['heading']
    ? $attributes['heading']
    : 'Aktuelle Ausstellungen';

$target_anchor = isset($attributes['targetAnchor']) ? trim((string) $attributes['targetAnchor']) : '';

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'cz-current-exhibitions']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php
    // One button, not a heading next to a button: label and chevrons are
    // both inside the same <button>, the whole thing is the click target.
    // This carries the theme's real button classes (wp-block-button__link
    // + friends, same as "Zur Galerie" on the front-page hero) and the
    // same inline style attribute a core/button instance gets when an
    // editor sets those properties by hand — confirmed against the live
    // "Zur Galerie" markup rather than guessed. Color, padding, font, and
    // hover/focus invert all come from wp-includes/blocks/button/style.min.css
    // and global.css's existing .wp-block-button__link:hover rule, not a
    // re-typed copy. No aria-label override: the visible label text is
    // the accessible name, same as "Zur Galerie" gets none either.
    //
    // Border-radius is NOT part of this inline style — it comes from this
    // block's own border support (block.json) + theme.json's default for
    // czemp-theme/current-exhibitions, both pointing at the same
    // --wp--custom--button--radius as core/button's own default. The
    // `selectors.border` entry in block.json re-targets that generated
    // CSS onto .cz-current-exhibitions__arrow specifically — this <button>,
    // not the outer wrapper div get_block_wrapper_attributes() applies to
    // below — same pattern core/button itself uses to style its inner
    // <a class="wp-block-button__link"> rather than the outer
    // .wp-block-button div.
    ?>
    <button
        type="button"
        class="cz-current-exhibitions__arrow wp-block-button__link has-text-color has-background has-custom-font-size wp-element-button"
        style="color:#ffffff;background-color:#1a1a1a;font-size:12px;letter-spacing:0.1em;text-transform:uppercase"
        <?php if ('' !== $target_anchor) : ?>
        data-target-anchor="<?php echo esc_attr($target_anchor); ?>"
        <?php endif; ?>
    >
        <span class="cz-current-exhibitions__heading"><?php echo esc_html($heading); ?></span>
        <svg class="cz-current-exhibitions__icon" viewBox="0 0 24 26" width="16" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline class="cz-current-exhibitions__chevron cz-current-exhibitions__chevron--top" points="6 5 12 10 18 5"></polyline>
            <polyline class="cz-current-exhibitions__chevron cz-current-exhibitions__chevron--middle" points="6 12 12 17 18 12"></polyline>
            <polyline class="cz-current-exhibitions__chevron cz-current-exhibitions__chevron--bottom" points="6 19 12 24 18 19"></polyline>
        </svg>
    </button>
</div>
