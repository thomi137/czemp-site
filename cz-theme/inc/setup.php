<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Theme support
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/global.css');
});

// Emoji-to-image conversion is dead weight for a site that doesn't rely
// on it — every modern browser renders emoji natively — and costs a
// blocking inline script plus (conditionally) a real request for
// wp-emoji-release.min.js on every single page load.
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// Register custom blocks
add_action('init', function () {
    register_block_type(get_stylesheet_directory() . '/blocks/gallery-item');
    register_block_type(get_stylesheet_directory() . '/blocks/artwork-list-item');
    register_block_type(get_stylesheet_directory() . '/blocks/sticky-nav');
    register_block_type(get_stylesheet_directory() . '/blocks/collection-subcategories');
    register_block_type(get_stylesheet_directory() . '/blocks/breadcrumbs');
    register_block_type(get_stylesheet_directory() . '/blocks/site-header');
    register_block_type(get_stylesheet_directory() . '/blocks/site-footer');
    register_block_type(get_stylesheet_directory() . '/blocks/latest-posts');
    register_block_type(get_stylesheet_directory() . '/blocks/event-archive');
    register_block_type(get_stylesheet_directory() . '/blocks/artwork-price');
    register_block_type(get_stylesheet_directory() . '/blocks/current-exhibitions');
    register_block_type(get_stylesheet_directory() . '/blocks/artwork-nav');
    register_block_type(get_stylesheet_directory() . '/blocks/animated-button');
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'cz-gallery-item-view',
        get_stylesheet_directory_uri() . '/blocks/gallery-item/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/gallery-item/view.js'),
        true
    );
    wp_enqueue_script(
        'cz-sticky-nav-view',
        get_stylesheet_directory_uri() . '/blocks/sticky-nav/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/sticky-nav/view.js'),
        true
    );
    wp_enqueue_script(
        'cz-current-exhibitions-view',
        get_stylesheet_directory_uri() . '/blocks/current-exhibitions/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/current-exhibitions/view.js'),
        true
    );
    wp_enqueue_script(
        'cz-animated-button-view',
        get_stylesheet_directory_uri() . '/blocks/animated-button/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/animated-button/view.js'),
        true
    );
    wp_enqueue_script(
        'cz-artwork-nav-view',
        get_stylesheet_directory_uri() . '/blocks/artwork-nav/view.js',
        [],
        filemtime(get_stylesheet_directory() . '/blocks/artwork-nav/view.js'),
        true
    );
    if (is_singular('artwork')) {
        wp_enqueue_script(
            'cz-artwork-nav-carousel',
            get_stylesheet_directory_uri() . '/blocks/artwork-nav/carousel.js',
            [],
            filemtime(get_stylesheet_directory() . '/blocks/artwork-nav/carousel.js'),
            true
        );
    }
    wp_enqueue_script(
        'cz-header-height',
        get_stylesheet_directory_uri() . '/build/js/header-height.js',
        [],
        filemtime(get_stylesheet_directory() . '/build/js/header-height.js'),
        true
    );
});

add_filter('script_loader_tag', function ($tag, $handle) {
    if ($handle === 'cz-sticky-nav-view') {
        return str_replace('<script ', '<script type="module" ', $tag);
    }
    return $tag;
}, 10, 2);

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'czemp-global',
        get_stylesheet_directory_uri() . '/assets/css/global.css',
        ['wp-block-library'],
        filemtime(get_stylesheet_directory() . '/assets/css/global.css')
    );
});

// Output WebP for all newly generated image sizes
add_filter('image_editor_output_format', function ($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    $formats['image/gif']  = 'image/webp';
    return $formats;
});
