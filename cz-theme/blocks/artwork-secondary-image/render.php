<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

if (!is_singular('artwork')) {
    return;
}

$image_id = (int) get_post_meta(get_the_ID(), 'secondary_image', true);
if (!$image_id) {
    return; // most artworks won't have one set — render nothing
}

$image_url = wp_get_attachment_image_url($image_id, 'large');
if (!$image_url) {
    return; // attachment was deleted/missing — degrade to nothing, not a broken <img>
}

// Rendered uncropped at native aspect ratio (see style.css) — the
// secondary_image_focal_x/_y meta is still stored via the "Sekundärbild"
// panel for a possible future cropped treatment, just unused here.
?>
<div <?php echo get_block_wrapper_attributes(['class' => 'cz-artwork-secondary-image']); ?>>
    <?php echo wp_get_attachment_image($image_id, 'large'); ?>
</div>
