<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Optional visibility window (start/end date) for regular Posts, so a
// Post can be used as a homepage highlight that automatically stops
// showing after a given date without anyone having to remember to remove
// it. Both fields are optional: empty start = visible immediately, empty
// end = never expires.
add_action('init', function () {
    register_post_meta('post', '_cz_post_start', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
        'default'      => '',
    ]);
    register_post_meta('post', '_cz_post_end', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
        'default'      => '',
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box(
        'cz_post_dates',
        'Anzeigezeitraum',
        'cz_post_dates_meta_box',
        'post',
        'side',
        'default'
    );
});

function cz_post_dates_meta_box($post) {
    wp_nonce_field('cz_post_dates', 'cz_post_dates_nonce');

    $start = get_post_meta($post->ID, '_cz_post_start', true);
    $end   = get_post_meta($post->ID, '_cz_post_end', true);
    ?>
    <p>
        <label for="cz-post-start"><strong>Sichtbar ab</strong></label><br>
        <input type="date" id="cz-post-start" name="cz_post_start" value="<?php echo esc_attr($start); ?>" style="width:100%">
    </p>
    <p>
        <label for="cz-post-end"><strong>Sichtbar bis</strong></label><br>
        <input type="date" id="cz-post-end" name="cz_post_end" value="<?php echo esc_attr($end); ?>" style="width:100%">
    </p>
    <p class="description">Beide Felder sind optional. Wird für die "Aktuelles"-Kacheln auf der Startseite verwendet — leer lassen, damit der Beitrag sofort bzw. dauerhaft berücksichtigt wird.</p>
    <?php
}

add_action('save_post_post', function ($post_id) {
    if (!isset($_POST['cz_post_dates_nonce']) || !wp_verify_nonce($_POST['cz_post_dates_nonce'], 'cz_post_dates')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    update_post_meta($post_id, '_cz_post_start', sanitize_text_field($_POST['cz_post_start'] ?? ''));
    update_post_meta($post_id, '_cz_post_end', sanitize_text_field($_POST['cz_post_end'] ?? ''));
});
