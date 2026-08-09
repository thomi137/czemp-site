<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

$today = current_time('Y-m-d');

// Small site, short list — filtering candidates in PHP after a plain
// query is simpler and less error-prone than a meta_query trying to
// express "empty or missing meta = unrestricted" for two independent
// optional date fields.
$candidates = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
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

$active = array_slice($active, 0, 3);

if (!$active) {
    return '';
}

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
