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
    <div id="cz-sticky-nav-panel" class="cz-sticky-nav__panel" role="dialog" aria-label="Navigation">
        <div class="cz-sticky-nav__drawer">
            <nav class="cz-sticky-nav__links"></nav>
        </div>
    </div>
    <div class="cz-spinner-overlay" aria-hidden="true">
        <div class="cz-spinner"></div>
    </div>
</div>
