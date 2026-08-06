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

// Cap query-pagination-numbers to at most 3 page-number links, centered on the current page
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

    return preg_replace_callback(
        '/<(a|span)\b[^>]*class="[^"]*page-numbers[^"]*"[^>]*>(\d+)<\/\1>/',
        function ($el) use ($min, $max) {
            $num = (int) $el[2];
            return ($num >= $min && $num <= $max) ? $el[0] : '';
        },
        $block_content
    );
}, 10, 2);
