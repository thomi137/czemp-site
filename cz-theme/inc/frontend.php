<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Subcollections: don't mix the parent page's artwork query with works from
// child collections (those now have their own overview tiles)
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !is_tax('collection')) {
        return;
    }

    $query->set('tax_query', [
        'relation' => 'AND',
        [
            'taxonomy'         => 'collection',
            'field'            => 'term_id',
            'terms'            => [get_queried_object_id()],
            'include_children' => false,
        ],
    ]);

    // Respect Claudia's manual drag order (see cz_sortable_get_order() in
    // inc/sortable.php) — no conditional needed here, unlike the admin
    // equivalent, since the tax_query above already guarantees exactly
    // one collection is in scope on this page. post__in also naturally
    // limits results to the term's own artworks, same as the tax_query
    // above already does, so pagination/perPage from the query block
    // still behaves as expected.
    $order = cz_sortable_get_order(get_queried_object_id());
    if ($order) {
        $query->set('post__in', $order);
        $query->set('orderby', 'post__in');
    }
});

// /galerie/ (the raw, ungrouped artwork archive) redirects permanently to
// /gallery/ (the collection overview) — the flat list of all artworks
// should no longer be directly reachable
add_action('template_redirect', function () {
    if (!is_post_type_archive('artwork')) {
        return;
    }
    $gallery_page = get_page_by_path('gallery');
    if (!$gallery_page) {
        return;
    }
    wp_safe_redirect(get_permalink($gallery_page->ID), 301);
    exit;
});

// Mark "Galerie" nav item as active on artwork and collection pages
add_filter('render_block', function (string $block_content, array $block) {
    if ($block['blockName'] !== 'core/navigation-link') {
        return $block_content;
    }
    if (!is_singular('artwork') && !is_post_type_archive('artwork') && !is_tax('collection')) {
        return $block_content;
    }
    $url          = trailingslashit($block['attrs']['url'] ?? '');
    $gallery_page = get_page_by_path('gallery');
    if (!$gallery_page || empty($url)) {
        return $block_content;
    }
    if (trailingslashit(get_permalink($gallery_page->ID)) === $url) {
        $block_content = str_replace(
            'class="wp-block-navigation-item ',
            'class="wp-block-navigation-item current-menu-item ',
            $block_content
        );
    }
    return $block_content;
}, 10, 2);

// The single-artwork featured image's rendered box is capped by CSS to the
// column width (see .single-artwork .wp-block-post-featured-image img in
// global.css, added for the "fit between header and footer" viewport-fit
// layout) — but WordPress computes the `sizes` attribute purely from the
// image's own intrinsic width, with no idea that CSS shrank its actual
// layout box. Left alone, that told the browser it might need up to
// 1066px/100vw and made it download the largest srcset candidate on
// almost every screen, even though it's now displayed far narrower.
// Approximates the column's real width per breakpoint (mobile stacks to
// full width; tablet/desktop the column is 55% up to a 1200px content
// cap) — not pixel-perfect, but close enough to make the browser pick a
// meaningfully smaller candidate.
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment) {
    if (!is_singular('artwork') || (int) $attachment->ID !== (int) get_post_thumbnail_id()) {
        return $attr;
    }
    $attr['sizes'] = '(max-width: 599px) calc(100vw - 40px), (max-width: 1279px) 55vw, 660px';
    return $attr;
}, 10, 2);

// Cap query-pagination-numbers to at most 3 page-number links, centered on
// the current page — but always keep page 1 and the last page themselves
// reachable regardless of that window, or there'd be no way back to the
// start once deep enough into pagination that page 1 fell outside it (and
// symmetrically, no way to jump straight to the end from page 1).
add_filter('render_block', function (string $block_content, array $block) {
    if ($block['blockName'] !== 'core/query-pagination-numbers') {
        return $block_content;
    }
    if (!preg_match('/class="[^"]*page-numbers current[^"]*"[^>]*>(\d+)</', $block_content, $m)) {
        return $block_content;
    }
    $current    = (int) $m[1];
    $max_shown  = 3;
    $half       = (int) floor(($max_shown - 1) / 2);
    $min        = max(1, $current - $half);
    $max        = $min + $max_shown - 1;

    preg_match_all('/<a\b[^>]*class="[^"]*page-numbers[^"]*"[^>]*>(\d+)<\/a>/', $block_content, $all);
    $last_page = $all[1] ? max(array_map('intval', $all[1])) : $current;

    return preg_replace_callback(
        '/<(a|span)\b[^>]*class="[^"]*page-numbers[^"]*"[^>]*>(\d+)<\/\1>/',
        function ($el) use ($min, $max, $last_page) {
            $num = (int) $el[2];
            $keep = ($num >= $min && $num <= $max) || $num === 1 || $num === $last_page;
            return $keep ? $el[0] : '';
        },
        $block_content
    );
}, 10, 2);
