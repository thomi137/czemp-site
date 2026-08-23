<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

if (!is_singular('artwork')) {
    return;
}

$post_id = get_the_ID();
$terms   = get_the_terms($post_id, 'collection');
if (!$terms || is_wp_error($terms)) {
    return;
}

// Same resolution breadcrumbs/render.php already uses when an artwork
// belongs to more than one Kollektion: the first term, no query-param
// tracking of "which collection did you arrive from". Same ambiguity
// that already exists in breadcrumbs today, not a new one.
$term = $terms[0];

// The real drag-and-drop order (see inc/sortable.php) — not menu_order,
// which is unused/zero for every artwork. Reused as-is, not reimplemented.
$order = cz_sortable_get_order($term->term_id);
$count = count($order);
$index = array_search($post_id, $order, true);

if (false === $index || $count < 2) {
    return; // nothing to navigate to
}

$prev_id = $order[($index - 1 + $count) % $count];
$next_id = $order[($index + 1) % $count];

// Deliberately not a wp-block-button__link — this button's whole look
// (circle, transparent + bordered by default, solid on hover) is its own
// design, not a hand-styled instance of the sitewide button
// invert-on-hover pattern (see .claude/rules/design-guidelines.md), so it
// stays off that class rather than fighting it for hover specificity. All
// visuals live in style.css.
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'cz-artwork-nav']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <a
        class="cz-artwork-nav__prev"
        href="<?php echo esc_url(get_permalink($prev_id)); ?>"
        aria-label="<?php echo esc_attr(sprintf('Vorheriges Werk: %s', get_the_title($prev_id))); ?>"
    >
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 4 7 12 15 20"></polyline>
        </svg>
    </a>
    <a
        class="cz-artwork-nav__next"
        href="<?php echo esc_url(get_permalink($next_id)); ?>"
        aria-label="<?php echo esc_attr(sprintf('Nächstes Werk: %s', get_the_title($next_id))); ?>"
    >
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="9 4 17 12 9 20"></polyline>
        </svg>
    </a>
</div>
