<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Register custom post type: Artwork
add_action('init', function () {
    register_post_type('artwork', [
        'labels' => [
            'name'               => 'Werke',
            'singular_name'      => 'Werk',
            'add_new_item'       => 'Neues Werk hinzufügen',
            'edit_item'          => 'Werk bearbeiten',
            'view_item'          => 'Werk ansehen',
            'search_items'       => 'Werke suchen',
            'not_found'          => 'Keine Werke gefunden',
            'not_found_in_trash' => 'Keine Werke im Papierkorb',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-art',
        'menu_position' => 3,
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt'],
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'galerie'],
    ]);

    register_taxonomy('collection', ['artwork', 'attachment'], [
        'labels' => [
            'name'          => 'Kollektionen',
            'singular_name' => 'Kollektion',
            'add_new_item'  => 'Neue Kollektion',
            'edit_item'     => 'Kollektion bearbeiten',
        ],
        'public'            => true,
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'kollektion'],
        'capabilities' => [
            'manage_terms' => 'edit_posts',
            'edit_terms'   => 'edit_posts',
            'delete_terms' => 'edit_posts',
            'assign_terms' => 'edit_posts',
        ],
    ]);
});

// Register price meta for artwork
add_action('init', function () {
    register_post_meta('artwork', 'price', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
    ]);
});

// Flush rewrite rules once after activation
add_action('after_switch_theme', function () {
    // Post type & taxonomy are registered via 'init'; flush after.
    flush_rewrite_rules();
});

// Sync the artwork's title and collection onto its featured image and
// attach it there ("Attached to"), so the Media Library stays usable
// instead of showing 254 identically-named files with no visible link
// to the artwork
function cz_sync_artwork_thumbnail($artwork_id) {
    $thumbnail_id = get_post_thumbnail_id($artwork_id);
    if (!$thumbnail_id) {
        return;
    }

    wp_update_post([
        'ID'          => $thumbnail_id,
        'post_title'  => get_the_title($artwork_id),
        'post_parent' => $artwork_id,
    ]);

    $term_ids = wp_get_post_terms($artwork_id, 'collection', ['fields' => 'ids']);
    if (is_wp_error($term_ids)) {
        return;
    }
    wp_set_object_terms($thumbnail_id, $term_ids, 'collection');
}

add_action('set_object_terms', function ($object_id, $terms, $tt_ids, $taxonomy) {
    if ($taxonomy !== 'collection' || get_post_type($object_id) !== 'artwork') {
        return;
    }
    cz_sync_artwork_thumbnail($object_id);
}, 10, 4);

foreach (['added_post_meta', 'updated_post_meta'] as $hook) {
    add_action($hook, function ($meta_id, $post_id, $meta_key) {
        if ($meta_key !== '_thumbnail_id' || get_post_type($post_id) !== 'artwork') {
            return;
        }
        cz_sync_artwork_thumbnail($post_id);
    }, 10, 3);
}

// Title changes on the artwork itself (without the featured image changing)
// are also propagated to the image
add_action('save_post_artwork', function ($post_id) {
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    cz_sync_artwork_thumbnail($post_id);
});
