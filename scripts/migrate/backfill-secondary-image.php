<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// One-off migration: context/history/features/feature-secondary-artwork-image.md step 5.
// Moves each artwork's text-embedded companion photo (a `core/image` block sitting in
// post_content from earlier manual edits, see that feature doc) into the new `secondary_image`
// post meta field (registered in inc/post-types.php, rendered by the new
// czemp-theme/artwork-secondary-image block) — leaving it in post_content too would show the
// photo twice once the new block ships.
//
// Usage (run against the live site — no staging, see CLAUDE.md):
//   wp eval-file scripts/migrate/backfill-secondary-image.php          # dry run
//   wp eval-file scripts/migrate/backfill-secondary-image.php apply    # writes
//
// (Plain "apply", not "--apply" — WP-CLI's own arg parser intercepts anything starting with
// "--" before it reaches $args.)
//
// The id -> attachment id map below was read directly off the live dataset (32 posts with a
// `core/image` block still in post_content), not estimated. Several share one image across
// multiple posts (the 1532/1305/1038/912/1689 groups below) — expected, same pattern as the
// Gold/Silber/Bronze trio.
//
// Idempotent: a post is skipped (not overwritten) if secondary_image is already set, and only
// touched if its content still contains exactly one core/image block for the expected
// attachment id — safe to re-run after a partial apply.

$apply = in_array( 'apply', $args, true );

$backfill = array(
    1344 => 844,
    1332 => 1333,
    1326 => 1532,
    1324 => 1532,
    1322 => 1532,
    1316 => 1305,
    1312 => 1305,
    1308 => 1305,
    1304 => 1305,
    1176 => 1177,
    1171 => 1172,
    1153 => 1154,
    1145 => 1146,
    1075 => 1076,
    1049 => 1038,
    1047 => 1038,
    1045 => 1038,
    1043 => 1038,
    1041 => 1038,
    1037 => 1038,
    1013 => 1014,
    1009 => 1010,
    1001 => 1002,
    1087 => 1689,
    1085 => 1689,
    979  => 1689,
    987  => 988,
    981  => 982,
    915  => 912,
    911  => 912,
    873  => 1688,
    869  => 870,
);

$ok      = 0;
$skipped = 0;

foreach ( $backfill as $post_id => $attachment_id ) {
    $post = get_post( $post_id );
    if ( ! $post || 'artwork' !== $post->post_type ) {
        WP_CLI::warning( "Post $post_id: not found or not an artwork — SKIPPED." );
        $skipped++;
        continue;
    }

    $existing = get_post_meta( $post_id, 'secondary_image', true );
    if ( $existing ) {
        WP_CLI::warning( "Post $post_id ({$post->post_title}): secondary_image already set to $existing — SKIPPED (not overwriting)." );
        $skipped++;
        continue;
    }

    $content     = $post->post_content;
    $pattern     = '#<!-- wp:image \{"id":' . preg_quote( (string) $attachment_id, '#' ) . '[,}].*?<!-- /wp:image -->\n?#s';
    $new_content = preg_replace( $pattern, '', $content, -1, $count );

    if ( 1 !== $count ) {
        WP_CLI::warning( "Post $post_id ({$post->post_title}): expected exactly 1 core/image block for attachment $attachment_id, found $count — SKIPPED, no changes." );
        $skipped++;
        continue;
    }

    if ( ! $apply ) {
        WP_CLI::log( "[dry-run] Post $post_id ({$post->post_title}): would set secondary_image=$attachment_id and remove 1 core/image block." );
        $ok++;
        continue;
    }

    update_post_meta( $post_id, 'secondary_image', $attachment_id );
    update_post_meta( $post_id, 'secondary_image_focal_x', 0.5 );
    update_post_meta( $post_id, 'secondary_image_focal_y', 0.5 );

    $result = wp_update_post( array(
        'ID'           => $post_id,
        'post_content' => $new_content,
    ), true );

    if ( is_wp_error( $result ) ) {
        WP_CLI::warning( "Post $post_id ({$post->post_title}): wp_update_post FAILED — " . $result->get_error_message() );
        $skipped++;
        continue;
    }

    WP_CLI::success( "Post $post_id ({$post->post_title}): secondary_image=$attachment_id set, core/image block removed." );
    $ok++;
}

WP_CLI::log( '' );
WP_CLI::log( ( $apply ? '' : '[dry-run] ' ) . "Done: $ok ok, $skipped skipped, out of " . count( $backfill ) . ' total.' );
