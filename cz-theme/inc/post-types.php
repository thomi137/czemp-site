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

// "Preis" sidebar panel on the Werk edit screen, so Claudia can set/change
// a price without touching code. The artwork-price block that displays
// this value lives in the single-artwork FSE template, which her Editor
// account can't open (see inc/admin.php) — this panel is the only place
// she can actually set it.
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    if ('artwork' !== get_current_screen()->post_type) {
        return;
    }
    wp_enqueue_script(
        'cz-artwork-price-panel',
        get_stylesheet_directory_uri() . '/assets/js/artwork-price-panel.js',
        ['wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data'],
        filemtime(get_stylesheet_directory() . '/assets/js/artwork-price-panel.js'),
        true
    );
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

// The reverse direction: tagging a not-yet-linked photo with a collection
// (Quick Edit, bulk edit, ...) auto-creates a draft Werk for it, so
// "upload + tag" is the whole workflow instead of a separate trip to
// Werke > Neu hinzufügen. Reuses cz_sync_artwork_thumbnail() (via
// set_post_thumbnail() below) rather than duplicating its title/parent/term
// sync.
function cz_maybe_create_artwork_from_attachment($attachment_id) {
    if (get_post_type($attachment_id) !== 'attachment' || !wp_attachment_is_image($attachment_id)) {
        return;
    }

    // Already "Uploaded to" something — a Werk it's already linked to
    // (including a trashed one: trashing doesn't clear post_parent, and
    // shouldn't spawn a replacement), or something unrelated entirely (e.g.
    // a photo embedded in a Page's own content). Either way, leave that
    // relationship alone rather than silently reassigning it — this also
    // catches cz_sync_artwork_thumbnail()'s own re-applied terms call
    // further down, which re-fires this same listener: by then post_parent
    // is already set, so it exits right here instead of looping. Only a
    // real, permanent deletion of the linked Werk clears post_parent back
    // to 0 (WordPress core's own cleanup on wp_delete_post()), which is
    // what actually allows a fresh Werk to be created next time.
    if (wp_get_post_parent_id($attachment_id)) {
        return;
    }

    $term_ids = wp_get_post_terms($attachment_id, 'collection', ['fields' => 'ids']);
    if (is_wp_error($term_ids) || empty($term_ids)) {
        return; // also covers term *removal*, which fires this hook with an empty list
    }

    $artwork_id = wp_insert_post([
        'post_type'   => 'artwork',
        'post_status' => 'draft',
        'post_title'  => get_the_title($attachment_id),
    ], true);
    if (is_wp_error($artwork_id)) {
        return;
    }

    // Terms must land on the new Werk before set_post_thumbnail() triggers
    // cz_sync_artwork_thumbnail() — that function reads the Werk's own
    // terms and pushes them onto the attachment; if it ran first it would
    // push an empty list and erase what was just tagged.
    wp_set_object_terms($artwork_id, $term_ids, 'collection');
    set_post_thumbnail($artwork_id, $attachment_id);
}

add_action('set_object_terms', function ($object_id, $terms, $tt_ids, $taxonomy) {
    if ($taxonomy !== 'collection' || get_post_type($object_id) !== 'attachment') {
        return;
    }
    cz_maybe_create_artwork_from_attachment($object_id);
}, 10, 4);
