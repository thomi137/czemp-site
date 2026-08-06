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

// Register custom blocks
add_action('init', function () {
    register_block_type(get_stylesheet_directory() . '/blocks/gallery-item');
    register_block_type(get_stylesheet_directory() . '/blocks/artwork-list-item');
    register_block_type(get_stylesheet_directory() . '/blocks/sticky-nav');
    register_block_type(get_stylesheet_directory() . '/blocks/collection-subcategories');
    register_block_type(get_stylesheet_directory() . '/blocks/breadcrumbs');
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
        'cz-header-height',
        get_stylesheet_directory_uri() . '/assets/js/header-height.js',
        [],
        filemtime(get_stylesheet_directory() . '/assets/js/header-height.js'),
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
