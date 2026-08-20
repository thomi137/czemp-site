<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Thumbnail + focal point for collections (term meta + media picker in admin)
add_action('init', function () {
    register_term_meta('collection', 'thumbnail_id', [
        'type'         => 'integer',
        'single'       => true,
        'show_in_rest' => true,
    ]);
    register_term_meta('collection', 'thumbnail_focal_x', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
        'default'      => 0.5,
    ]);
    register_term_meta('collection', 'thumbnail_focal_y', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
        'default'      => 0.5,
    ]);
});

// Returns the thumbnail preview markup (image + focal point marker) for the term editor
function cz_collection_thumbnail_preview_html($thumbnail_url, $focal_x, $focal_y) {
    if (!$thumbnail_url) {
        return '';
    }
    return sprintf(
        '<div class="cz-focal-point-picker"><img src="%s" alt=""><div class="cz-focal-point-marker" style="left:%s%%;top:%s%%;"></div></div>',
        esc_url($thumbnail_url),
        esc_attr($focal_x * 100),
        esc_attr($focal_y * 100)
    );
}

add_action('collection_add_form_fields', function () {
    ?>
    <div class="form-field">
        <label for="cz-collection-thumbnail-button">Vorschaubild</label>
        <input type="hidden" name="thumbnail_id" id="cz-collection-thumbnail-id" value="">
        <input type="hidden" name="thumbnail_focal_x" id="cz-collection-thumbnail-focal-x" value="0.5">
        <input type="hidden" name="thumbnail_focal_y" id="cz-collection-thumbnail-focal-y" value="0.5">
        <div id="cz-collection-thumbnail-preview"></div>
        <button type="button" class="button" id="cz-collection-thumbnail-button">Bild auswählen</button>
        <button type="button" class="button" id="cz-collection-thumbnail-remove" style="display:none;">Entfernen</button>
        <p>Wird für die Unterkategorie-Kacheln auf der übergeordneten Kollektionsseite verwendet. Klick ins Bild, um den Bildausschnitt festzulegen.</p>
    </div>
    <?php
});

add_action('collection_edit_form_fields', function ($term) {
    $thumbnail_id  = (int) get_term_meta($term->term_id, 'thumbnail_id', true);
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';
    $focal_x       = get_term_meta($term->term_id, 'thumbnail_focal_x', true);
    $focal_y       = get_term_meta($term->term_id, 'thumbnail_focal_y', true);
    $focal_x       = ($focal_x === '') ? 0.5 : (float) $focal_x;
    $focal_y       = ($focal_y === '') ? 0.5 : (float) $focal_y;
    ?>
    <tr class="form-field">
        <th scope="row"><label for="cz-collection-thumbnail-button">Vorschaubild</label></th>
        <td>
            <input type="hidden" name="thumbnail_id" id="cz-collection-thumbnail-id" value="<?php echo esc_attr($thumbnail_id); ?>">
            <input type="hidden" name="thumbnail_focal_x" id="cz-collection-thumbnail-focal-x" value="<?php echo esc_attr($focal_x); ?>">
            <input type="hidden" name="thumbnail_focal_y" id="cz-collection-thumbnail-focal-y" value="<?php echo esc_attr($focal_y); ?>">
            <div id="cz-collection-thumbnail-preview"><?php echo cz_collection_thumbnail_preview_html($thumbnail_url, $focal_x, $focal_y); ?></div>
            <button type="button" class="button" id="cz-collection-thumbnail-button">Bild auswählen</button>
            <button type="button" class="button" id="cz-collection-thumbnail-remove" style="<?php echo $thumbnail_id ? '' : 'display:none;'; ?>">Entfernen</button>
            <p class="description">Wird für die Unterkategorie-Kacheln auf der übergeordneten Kollektionsseite verwendet. Klick ins Bild, um den Bildausschnitt festzulegen.</p>
        </td>
    </tr>
    <?php
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['edit-tags.php', 'term.php'], true)) {
        return;
    }
    if (($_GET['taxonomy'] ?? '') !== 'collection') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style(
        'cz-collection-thumbnail-picker',
        get_stylesheet_directory_uri() . '/assets/css/collection-thumbnail-picker.css',
        [],
        filemtime(get_stylesheet_directory() . '/assets/css/collection-thumbnail-picker.css')
    );
    wp_enqueue_script(
        'cz-collection-thumbnail-picker',
        get_stylesheet_directory_uri() . '/build/js/collection-thumbnail-picker.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/build/js/collection-thumbnail-picker.js'),
        true
    );
});

foreach (['create_collection', 'edited_collection'] as $hook) {
    add_action($hook, function ($term_id) {
        if (isset($_POST['thumbnail_id'])) {
            $thumbnail_id = absint($_POST['thumbnail_id']);
            if ($thumbnail_id) {
                update_term_meta($term_id, 'thumbnail_id', $thumbnail_id);
            } else {
                delete_term_meta($term_id, 'thumbnail_id');
            }
        }
        if (isset($_POST['thumbnail_focal_x'], $_POST['thumbnail_focal_y'])) {
            update_term_meta($term_id, 'thumbnail_focal_x', max(0, min(1, (float) $_POST['thumbnail_focal_x'])));
            update_term_meta($term_id, 'thumbnail_focal_y', max(0, min(1, (float) $_POST['thumbnail_focal_y'])));
        }
    });
}
