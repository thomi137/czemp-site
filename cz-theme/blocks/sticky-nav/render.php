<?php
$wrapper = get_block_wrapper_attributes(['class' => 'cz-sticky-nav']);
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
    <div id="cz-sticky-nav-panel" class="cz-sticky-nav__panel" aria-hidden="true" role="dialog" aria-label="Navigation">
        <div class="cz-sticky-nav__backdrop" aria-hidden="true"></div>
        <div class="cz-sticky-nav__drawer">
            <button class="cz-sticky-nav__close" aria-label="Schliessen"><span></span><span></span></button>
            <nav class="cz-sticky-nav__links"></nav>
        </div>
    </div>
</div>
