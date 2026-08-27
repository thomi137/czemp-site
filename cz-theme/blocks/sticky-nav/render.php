<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Preview switch, set via Settings -> Menü-Stil (inc/nav-style.php) - this
// block is only ever instantiated from the hardcoded string in
// site-footer/render.php, never opened directly in the block editor, so
// the toggle lives in wp-admin rather than an Inspector control here. One
// modifier class on the outer wrapper reaches both places nav links
// actually render: the desktop bar (.cz-sticky-nav__inner) and the
// mobile drawer (.cz-sticky-nav__links, populated by view.js cloning the
// desktop markup client-side), since both live inside this wrapper.
$button_style = !empty($attributes['buttonStyle']);
$wrapper_class = 'cz-sticky-nav' . ($button_style ? ' cz-sticky-nav--buttons' : '');
$wrapper = get_block_wrapper_attributes(['class' => $wrapper_class]);
?>
<div <?php echo $wrapper; ?>>
    <div class="cz-sticky-nav__bar">
        <button class="cz-sticky-nav__open" aria-label="Navigation öffnen" aria-expanded="false" aria-controls="cz-sticky-nav-panel">
            <span></span><span></span><span></span>
        </button>
        <div class="cz-sticky-nav__inner">
            <?php echo $content; ?>
        </div>
    </div>
    <div id="cz-sticky-nav-panel" class="cz-sticky-nav__panel" role="dialog" aria-label="Navigation">
        <div class="cz-sticky-nav__drawer">
            <nav class="cz-sticky-nav__links"></nav>
        </div>
    </div>
    <div class="cz-spinner-overlay" aria-hidden="true">
        <div class="cz-spinner"></div>
    </div>
</div>
