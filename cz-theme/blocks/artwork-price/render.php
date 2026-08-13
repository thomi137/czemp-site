<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

if (!is_singular('artwork')) {
    return;
}

$price = get_post_meta(get_the_ID(), 'price', true);
if ($price === '' || $price === null) {
    return; // most artworks won't have one set — render nothing, not an empty tag
}
?>
<p <?php echo get_block_wrapper_attributes(['class' => 'cz-artwork-price']); ?>>
    <?php echo esc_html($price); ?>
</p>
