<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Still called currentYearOnly in the stored attribute (see edit.js for
// why renaming the actual key isn't safe) — $upcoming_only is just this
// function's own local name for what the toggle now means: "not yet
// ended" rather than "published this calendar year".
$upcoming_only    = $attributes['currentYearOnly'] ?? true;
$past_events_url  = $attributes['pastEventsUrl'] ?? '';

$candidates = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

// Only posts with an actual visibility window set count as "events" for
// archive purposes — undated posts (see czemp-theme/latest-posts) stay
// homepage-only and don't clutter what's meant to be a chronological
// record of actual announcements. A post with neither field set never
// reaches here at all, so it can never end up in the "past" bucket below.
$dated = array_values(array_filter($candidates, function ($post) {
    $start = get_post_meta($post->ID, '_cz_post_start', true);
    $end   = get_post_meta($post->ID, '_cz_post_end', true);
    return $start !== '' || $end !== '';
}));

// Sort/group by the event's own start date, not when it was published —
// a post announced today about a show two months from now belongs with
// other upcoming events, not at the top by publish date. A post with no
// start date falls back to its publish date instead, sorted alongside
// posts that do have one. substr() normalizes post_date (a full
// YYYY-MM-DD HH:MM:SS) down to the same YYYY-MM-DD shape as the meta
// fields, so string comparison always compares like with like.
$event_date = function ($post) {
    $start = get_post_meta($post->ID, '_cz_post_start', true);
    return $start !== '' ? $start : substr($post->post_date, 0, 10);
};

// Whether an event has ended is judged purely by _cz_post_end, computed
// fresh on every render — no separate "archived" flag is ever stored, so
// clearing the end date on an already-past post makes it current again
// automatically, with nothing else to update.
$today = current_time('Y-m-d');
$has_ended = function ($post) use ($today) {
    $end = get_post_meta($post->ID, '_cz_post_end', true);
    return $end !== '' && $end < $today;
};

if ($upcoming_only) {
    // Anything not yet ended — no end date at all means ongoing/open-
    // ended, and it never moves to the past bucket on its own. Soonest
    // first, no year sub-grouping: this is meant to be a short,
    // forward-looking list, not a historical record.
    $shown = array_values(array_filter($dated, function ($post) use ($has_ended) {
        return !$has_ended($post);
    }));
    usort($shown, function ($a, $b) use ($event_date) {
        return $event_date($a) <=> $event_date($b);
    });
    $grouped = $shown ? [0 => $shown] : [];
} else {
    // Already-ended items only, grouped by the event's own year (not
    // publish year), most recent year and most-recently-ended-first
    // within each year.
    $past = array_values(array_filter($dated, $has_ended));
    usort($past, function ($a, $b) use ($event_date) {
        return $event_date($b) <=> $event_date($a);
    });
    $grouped = [];
    foreach ($past as $post) {
        $year = (int) mysql2date('Y', $event_date($post));
        $grouped[$year][] = $post;
    }
    krsort($grouped);
}

if (!$grouped) {
    return '';
}

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'cz-event-archive']);

global $post;
$original_post = $post;
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php foreach ($grouped as $year => $year_posts) : ?>
        <?php if (!$upcoming_only) : ?>
            <h2 class="cz-event-archive__year"><?php echo esc_html($year); ?></h2>
        <?php endif; ?>
        <div class="cz-events-grid">
            <?php
            foreach ($year_posts as $year_post) {
                $post = $year_post;
                setup_postdata($post);
                echo apply_filters('the_content', $post->post_content);
            }
            ?>
        </div>
    <?php endforeach; ?>

    <?php if ($upcoming_only && $past_events_url) : ?>
        <p class="cz-event-archive__past-link">
            <a
                class="wp-block-button__link has-text-color has-background has-custom-font-size wp-element-button"
                href="<?php echo esc_url($past_events_url); ?>"
                style="color:#ffffff;background-color:#1a1a1a;font-size:12px;letter-spacing:0.1em;text-transform:uppercase"
            >Vergangene Veranstaltungen ansehen</a>
        </p>
    <?php endif; ?>
</div>
<?php
$post = $original_post;
wp_reset_postdata();
