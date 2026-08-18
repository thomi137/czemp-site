<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// One-off migration: move "Fr. X.-" prices out of the free-text artwork
// excerpt into the `price` post meta field (already registered in
// inc/post-types.php, already rendered by the artwork-price block and
// artwork-list-item — just never populated).
//
// Usage (run against the live site — no staging, see CLAUDE.md):
//   wp eval-file scripts/migrate/backfill-prices.php          # dry run
//   wp eval-file scripts/migrate/backfill-prices.php apply    # writes
//
// (Plain "apply", not "--apply" — WP-CLI's own arg parser intercepts
// anything starting with "--" before it reaches $args.)
//
// Three cases, decided against a full read of the live dataset first:
//
//   1. Excerpt has exactly one "Fr. X.-" price at the tail -> migrate:
//      move it to `price` meta, strip it (+ its leading ", ") off the
//      excerpt.
//   2. Excerpt has two prices ("Fr. 800.- Fr. 640.-" style - original +
//      "ART SALE" discounted price) -> left completely untouched,
//      excerpt and price meta both, per explicit instruction. Not
//      migrated, not flagged.
//   3. Excerpt ends in a dangling "Fr. " with no number after it (one
//      known case, a pre-existing data typo) -> strip the dangling
//      "Fr. " off the excerpt, leave `price` meta blank, flag for manual
//      entry.
//
// Idempotent: a post is only touched if its excerpt still matches case 1
// or case 3, so re-running after a partial --apply is safe.

$apply = in_array( 'apply', $args, true );

$price_re   = '/Fr\.?[\s\x{00A0}]*\d[\d.,]*\.?-?/u';
$anomaly_re = '/Fr\.?[\s\x{00A0}]*$/u';

$post_ids = get_posts( [
    'post_type'   => 'artwork',
    'post_status' => 'publish',
    'numberposts' => -1,
    'fields'      => 'ids',
] );

$migrated       = 0;
$skipped_dual   = 0;
$stripped_only  = 0;
$untouched      = 0;
$needs_manual   = [];

function cz_strip_price_tail( $excerpt, $offset ) {
    $new_excerpt = rtrim( substr( $excerpt, 0, $offset ) );
    $new_excerpt = rtrim( $new_excerpt, ',' );
    return rtrim( $new_excerpt );
}

foreach ( $post_ids as $post_id ) {
    $post    = get_post( $post_id );
    $excerpt = $post->post_excerpt;

    preg_match_all( $price_re, $excerpt, $matches, PREG_OFFSET_CAPTURE );
    $count = count( $matches[0] );

    if ( 1 === $count ) {
        list( $price_text, $offset ) = $matches[0][0];
        $price       = trim( $price_text );
        $new_excerpt = cz_strip_price_tail( $excerpt, $offset );

        printf( "MIGRATE #%d: price=\"%s\"\n  old: %s\n  new: %s\n\n", $post_id, $price, $excerpt, $new_excerpt );

        if ( $apply ) {
            wp_update_post( [ 'ID' => $post_id, 'post_excerpt' => $new_excerpt ] );
            update_post_meta( $post_id, 'price', $price );
        }
        $migrated++;
    } elseif ( $count >= 2 ) {
        // Art Sale (original + discounted price) - leave untouched.
        $skipped_dual++;
    } elseif ( preg_match( $anomaly_re, $excerpt, $m, PREG_OFFSET_CAPTURE ) ) {
        $offset      = $m[0][1];
        $new_excerpt = cz_strip_price_tail( $excerpt, $offset );

        printf( "STRIP-ONLY #%d (no price number found, needs manual entry):\n  old: %s\n  new: %s\n\n", $post_id, $excerpt, $new_excerpt );

        if ( $apply ) {
            wp_update_post( [ 'ID' => $post_id, 'post_excerpt' => $new_excerpt ] );
        }
        $needs_manual[] = $post_id;
        $stripped_only++;
    } else {
        $untouched++;
    }
}

printf( "\n--- Summary (%s) ---\n", $apply ? 'APPLIED' : 'DRY RUN' );
printf( "Migrated (price moved to meta):     %d\n", $migrated );
printf( "Skipped (Art Sale, untouched):      %d\n", $skipped_dual );
printf( "Stripped only (needs manual price): %d\n", $stripped_only );
printf( "Untouched (no price text):          %d\n", $untouched );

if ( $needs_manual ) {
    echo "\nNeeds manual price entry:\n";
    foreach ( $needs_manual as $id ) {
        printf( "  #%d - %s - %s\n", $id, get_the_title( $id ), get_permalink( $id ) );
    }
}
