<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

$current_year_only = $attributes['currentYearOnly'] ?? true;
$past_events_url    = $attributes['pastEventsUrl'] ?? '';

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
// record of actual announcements.
$dated = array_values(array_filter($candidates, function ($post) {
    $start = get_post_meta($post->ID, '_cz_post_start', true);
    $end   = get_post_meta($post->ID, '_cz_post_end', true);
    return $start !== '' || $end !== '';
}));

$this_year = (int) current_time('Y');

$grouped = [];
foreach ($dated as $post) {
    $year = (int) mysql2date('Y', $post->post_date);
    $grouped[$year][] = $post;
}
krsort($grouped);

if ($current_year_only) {
    $grouped = array_intersect_key($grouped, [$this_year => true]);
} else {
    unset($grouped[$this_year]);
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
        <?php if (!$current_year_only) : ?>
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

    <?php if ($current_year_only && $past_events_url) : ?>
        <p class="cz-event-archive__past-link">
            <a
                class="wp-block-button__link has-text-color has-background has-custom-font-size wp-element-button"
                href="<?php echo esc_url($past_events_url); ?>"
                style="border-radius:0;color:#ffffff;background-color:#1a1a1a;font-size:12px;letter-spacing:0.1em;text-transform:uppercase"
            >Vergangene Veranstaltungen ansehen</a>
        </p>
    <?php endif; ?>
</div>
<?php
$post = $original_post;
wp_reset_postdata();
