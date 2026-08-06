<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Migration REST endpoints — token-protected, no Basic Auth needed
add_action('rest_api_init', function () {
    $token_check = function (WP_REST_Request $req) {
        $token = defined('CZ_MIGRATE_TOKEN') ? CZ_MIGRATE_TOKEN : '';
        return $token && $req->get_header('X-Migrate-Token') === $token
            ? true
            : new WP_Error('rest_forbidden', 'Ungültiger Token.', ['status' => 403]);
    };

    register_rest_route('czemp/v1', '/collection', [
        'methods'             => 'POST',
        'permission_callback' => $token_check,
        'callback'            => function (WP_REST_Request $req) {
            $name   = sanitize_text_field($req->get_param('name'));
            $slug   = sanitize_title($req->get_param('slug') ?: $name);
            $parent = intval($req->get_param('parent'));
            $args   = ['slug' => $slug];
            if ($parent) {
                $args['parent'] = $parent;
            }
            $term = wp_insert_term($name, 'collection', $args);
            if (is_wp_error($term)) {
                return new WP_Error('term_error', $term->get_error_message(), ['status' => 400]);
            }
            return ['id' => $term['term_id'], 'name' => $name, 'slug' => $slug, 'parent' => $parent];
        },
    ]);

    register_rest_route('czemp/v1', '/artwork', [
        'methods'             => 'POST',
        'permission_callback' => $token_check,
        'callback'            => function (WP_REST_Request $req) {
            $post_id = wp_insert_post([
                'post_type'    => 'artwork',
                'post_status'  => 'publish',
                'post_title'   => sanitize_text_field($req->get_param('title')),
                'post_excerpt' => sanitize_textarea_field($req->get_param('excerpt')),
                'post_content' => wp_kses_post($req->get_param('content')),
            ], true);
            if (is_wp_error($post_id)) {
                return new WP_Error('post_error', $post_id->get_error_message(), ['status' => 400]);
            }
            $collection = intval($req->get_param('collection'));
            if ($collection) {
                wp_set_object_terms($post_id, $collection, 'collection');
            }
            $media = intval($req->get_param('featured_media'));
            if ($media) {
                set_post_thumbnail($post_id, $media);
            }
            $price = sanitize_text_field($req->get_param('price'));
            if ($price !== '') {
                update_post_meta($post_id, 'price', $price);
            }
            return ['id' => $post_id, 'title' => get_the_title($post_id)];
        },
    ]);

    register_rest_route('czemp/v1', '/media', [
        'methods'             => 'POST',
        'permission_callback' => $token_check,
        'callback'            => function (WP_REST_Request $req) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $filename = sanitize_file_name($req->get_header('X-Filename') ?: 'upload');
            $data     = $req->get_body();
            $tmp      = wp_tempnam($filename);
            file_put_contents($tmp, $data);

            $file = ['name' => $filename, 'tmp_name' => $tmp, 'error' => 0, 'size' => strlen($data)];
            $id   = media_handle_sideload($file, 0);
            @unlink($tmp);

            if (is_wp_error($id)) {
                return new WP_Error('media_error', $id->get_error_message(), ['status' => 400]);
            }
            return ['id' => $id, 'url' => wp_get_attachment_url($id)];
        },
    ]);

    // One-off backfill: retroactively give existing artwork featured images
    // their title, collection, and attachment link (for artworks from the
    // CSV migration)
    register_rest_route('czemp/v1', '/backfill-media-metadata', [
        'methods'             => 'POST',
        'permission_callback' => $token_check,
        'callback'            => function () {
            $artwork_ids = get_posts([
                'post_type'      => 'artwork',
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);

            $updated = 0;
            $skipped = 0;
            foreach ($artwork_ids as $artwork_id) {
                $before = get_post_thumbnail_id($artwork_id);
                if (!$before) {
                    $skipped++;
                    continue;
                }
                cz_sync_artwork_thumbnail($artwork_id);
                $updated++;
            }

            return ['updated' => $updated, 'skipped' => $skipped, 'total' => count($artwork_ids)];
        },
    ]);
});
