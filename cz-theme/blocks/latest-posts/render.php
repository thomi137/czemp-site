<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

$today = current_time('Y-m-d');

// Small site, short list — filtering candidates in PHP after a plain
// query is simpler and less error-prone than a meta_query trying to
// express "empty or missing meta = unrestricted" for two independent
// optional date fields. The query's own orderby is irrelevant either
// way since $active gets re-sorted by effective date below.
$candidates = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'no_found_rows'  => true,
]);

$active = array_values(array_filter($candidates, function ($post) use ($today) {
    $start = get_post_meta($post->ID, '_cz_post_start', true);
    $end   = get_post_meta($post->ID, '_cz_post_end', true);
    if ($start && $start > $today) {
        return false;
    }
    if ($end && $end < $today) {
        return false;
    }
    return true;
}));

if (!$active) {
    return '';
}

// Sort by the post's own effective date, not raw publish date — a post
// with an explicit _cz_post_start belongs by that date, not by whenever
// it happened to be published (e.g. a just-published post announcing an
// event that started earlier shouldn't jump ahead of one whose own start
// date is more recent). Falls back to the publish date when unset. Same
// "effective date" rule as blocks/event-archive/render.php's $event_date,
// applied here independently since the two blocks don't share code.
// Most recent effective date first (descending) — same direction as
// before, only the sort key changed.
$effective_date = function ($post) {
    $start = get_post_meta($post->ID, '_cz_post_start', true);
    return $start !== '' ? $start : substr($post->post_date, 0, 10);
};
usort($active, function ($a, $b) use ($effective_date) {
    return $effective_date($b) <=> $effective_date($a);
});

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'cz-showcase-grid']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php
    global $post;
    $original_post = $post;
    foreach ($active as $post) {
        setup_postdata($post);
        echo apply_filters('the_content', $post->post_content);
    }
    $post = $original_post;
    wp_reset_postdata();
    ?>
</div>
