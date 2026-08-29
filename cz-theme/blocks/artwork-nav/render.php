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

// Which Kollektion the visitor is actually browsing, if known: set by
// artwork-list-item/render.php on every link out of a /kollektion/.../
// archive, and echoed back onto our own prev/next hrefs below so it
// keeps carrying forward click after click. Falls back to the artwork's
// first assigned term (same resolution breadcrumbs/render.php uses) the
// same way it always has whenever there's no context - direct link,
// search engine visit, or a foreign/stale param. is_object_in_term()
// guards against trusting a param for a collection this artwork isn't
// even in.
$requested_term_id = isset($_GET['kollektion']) ? absint($_GET['kollektion']) : 0;
$has_context        = $requested_term_id && is_object_in_term($post_id, 'collection', [$requested_term_id]);
$term_id            = $has_context ? $requested_term_id : $terms[0]->term_id;

// The real drag-and-drop order (see inc/sortable.php) — not menu_order,
// which is unused/zero for every artwork. Reused as-is, not reimplemented.
$order = cz_sortable_get_order($term_id);
$count = count($order);
$index = array_search($post_id, $order, true);

if (false === $index || $count < 2) {
    return; // nothing to navigate to
}

$prev_id = $order[($index - 1 + $count) % $count];
$next_id = $order[($index + 1) % $count];

$prev_url = $has_context ? add_query_arg('kollektion', $term_id, get_permalink($prev_id)) : get_permalink($prev_id);
$next_url = $has_context ? add_query_arg('kollektion', $term_id, get_permalink($next_id)) : get_permalink($next_id);

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
        href="<?php echo esc_url($prev_url); ?>"
        aria-label="<?php echo esc_attr(sprintf('Vorheriges Werk: %s', get_the_title($prev_id))); ?>"
    >
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 4 7 12 15 20"></polyline>
        </svg>
    </a>
    <a
        class="cz-artwork-nav__next"
        href="<?php echo esc_url($next_url); ?>"
        aria-label="<?php echo esc_attr(sprintf('Nächstes Werk: %s', get_the_title($next_id))); ?>"
    >
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="9 4 17 12 9 20"></polyline>
        </svg>
    </a>
</div>
