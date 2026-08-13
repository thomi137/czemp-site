<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

/**
 * SEO: <meta name="description">, Open Graph, Twitter Card.
 *
 * No SEO plugin is installed — this is a deliberately small, hand-rolled
 * replacement covering the actual social-sharing surface. Facebook,
 * WhatsApp, Telegram, Signal and iMessage link previews all read the same
 * Open Graph tags — there's no separate "whatsapp:" tag namespace to add.
 * X reads its own "twitter:" tags; the meta prefix predates the rename
 * and hasn't changed since.
 *
 * Per-item title/description/image are fully automatic (excerpt/content,
 * featured image, term thumbnail — whatever's already there, no extra
 * editing required). The only manual settings are the site-wide fallback
 * description/image used where a page has neither (e.g. the homepage),
 * under Settings > SEO & Social — admin-only, same pattern as
 * Wartungsmodus.
 */

add_action('after_setup_theme', function () {
    // 1200×630 is the width/height OG/Twitter previews are built around;
    // registering it here means new uploads get a properly-cropped social
    // image automatically. Existing media falls back to its next-closest
    // already-generated size (see cz_seo_image_from_attachment()) until
    // regenerated — still a valid, correctly-sized preview, just not
    // necessarily cropped to this exact ratio.
    add_image_size('cz-social', 1200, 630, true);
});

add_action('admin_menu', function () {
    $title = 'SEO & Social';
    add_options_page($title, $title, 'manage_options', 'cz-seo', 'cz_render_seo_settings_page');
});

add_action('admin_init', function () {
    register_setting('cz_seo', 'cz_seo_default_description', ['type' => 'string', 'default' => '']);
    register_setting('cz_seo', 'cz_seo_default_image_id', ['type' => 'integer', 'default' => 0]);
});

function cz_render_seo_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $image_id  = (int) get_option('cz_seo_default_image_id');
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    ?>
    <div class="wrap">
        <h1>SEO & Social</h1>
        <p>
            Gilt nur als Rückfalloption für Seiten ohne eigenen Textauszug/eigenes Beitragsbild
            (z. B. die Startseite). Werke, Beiträge, Seiten und Kollektionen verwenden automatisch
            ihren eigenen Textauszug bzw. ihr eigenes Beitrags-/Vorschaubild.
        </p>
        <form method="post" action="options.php">
            <?php settings_fields('cz_seo'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="cz-seo-default-description">Standard-Beschreibung</label></th>
                    <td>
                        <textarea name="cz_seo_default_description" id="cz-seo-default-description" rows="3" class="large-text"><?php echo esc_textarea(get_option('cz_seo_default_description')); ?></textarea>
                        <p class="description">
                            Wird für die Startseite verwendet, sofern kein Untertitel gesetzt ist
                            (Einstellungen &rarr; Allgemein), sowie als letzter Rückfall überall sonst.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Standard-Bild</th>
                    <td>
                        <input type="hidden" name="cz_seo_default_image_id" id="cz-seo-default-image-id" value="<?php echo esc_attr($image_id); ?>">
                        <div id="cz-seo-default-image-preview">
                            <?php if ($image_url) : ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width:300px;height:auto;display:block;margin-bottom:8px;">
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button" id="cz-seo-default-image-button">Bild auswählen</button>
                        <button type="button" class="button" id="cz-seo-default-image-remove" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Entfernen</button>
                        <p class="description">Wird verwendet, wenn eine Seite kein eigenes Beitragsbild hat (z. B. das Wappen/Logo als Rückfall).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Speichern'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'settings_page_cz-seo') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script(
        'cz-seo-image-picker',
        get_stylesheet_directory_uri() . '/assets/js/seo-image-picker.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/assets/js/seo-image-picker.js'),
        true
    );
});

// ── Per-post override ────────────────────────────────────────────────

// Automatic (excerpt/content-derived) descriptions break down for pages
// whose actual visible text lives in the *template* rather than the
// post's own post_content — page-gallery.html's intro paragraph and
// artwork grid come from a pattern + a query block, not from the Galerie
// page's own content, so get_the_excerpt() on it has nothing to read.
// This gives any post/page/artwork an explicit escape hatch.
add_action('add_meta_boxes', function () {
    foreach (['post', 'page', 'artwork'] as $post_type) {
        add_meta_box('cz_seo', 'SEO & Social', 'cz_render_seo_meta_box', $post_type, 'side', 'default');
    }
});

function cz_render_seo_meta_box($post) {
    wp_nonce_field('cz_seo_meta_box', 'cz_seo_meta_box_nonce');
    $description = get_post_meta($post->ID, '_cz_seo_description', true);
    $image_id    = (int) get_post_meta($post->ID, '_cz_seo_image_id', true);
    $image_url   = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    ?>
    <p>
        <label for="cz-seo-description">Beschreibung</label><br>
        <textarea name="cz_seo_description" id="cz-seo-description" rows="3" style="width:100%;"><?php echo esc_textarea($description); ?></textarea>
    </p>
    <p class="description">
        Für Suchmaschinen und Linkvorschauen (Facebook, WhatsApp, X, …). Leer lassen, um automatisch
        den Textauszug zu verwenden.
    </p>
    <p>
        <label>Bild</label><br>
        <input type="hidden" name="cz_seo_image_id" class="cz-seo-image-id" value="<?php echo esc_attr($image_id); ?>">
        <span class="cz-seo-image-preview">
            <?php if ($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width:100%;height:auto;display:block;margin-bottom:6px;">
            <?php endif; ?>
        </span>
        <button type="button" class="button cz-seo-image-button">Bild wählen</button>
        <button type="button" class="button cz-seo-image-remove" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Entfernen</button>
    </p>
    <p class="description">Leer lassen, um automatisch das Beitragsbild zu verwenden.</p>
    <?php
}

add_action('save_post', function ($post_id) {
    if (!isset($_POST['cz_seo_meta_box_nonce']) || !wp_verify_nonce($_POST['cz_seo_meta_box_nonce'], 'cz_seo_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['cz_seo_description'])) {
        update_post_meta($post_id, '_cz_seo_description', sanitize_textarea_field(wp_unslash($_POST['cz_seo_description'])));
    }
    if (isset($_POST['cz_seo_image_id'])) {
        $image_id = absint($_POST['cz_seo_image_id']);
        if ($image_id) {
            update_post_meta($post_id, '_cz_seo_image_id', $image_id);
        } else {
            delete_post_meta($post_id, '_cz_seo_image_id');
        }
    }
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    if (!in_array(get_current_screen()->post_type, ['post', 'page', 'artwork'], true)) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script(
        'cz-seo-post-image-picker',
        get_stylesheet_directory_uri() . '/assets/js/seo-post-image-picker.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/assets/js/seo-post-image-picker.js'),
        true
    );
});

// ── Resolving what to output ────────────────────────────────────────

function cz_seo_image_from_attachment($attachment_id) {
    $src = wp_get_attachment_image_src($attachment_id, 'cz-social');
    if (!$src) {
        return null;
    }
    return ['url' => $src[0], 'width' => $src[1], 'height' => $src[2]];
}

function cz_seo_default_image() {
    $image_id = (int) get_option('cz_seo_default_image_id');
    if ($image_id) {
        $image = cz_seo_image_from_attachment($image_id);
        if ($image) {
            return $image;
        }
    }
    $site_icon_id = get_option('site_icon');
    return $site_icon_id ? cz_seo_image_from_attachment((int) $site_icon_id) : null;
}

// get_the_excerpt() already falls back from a manual excerpt to an
// auto-trimmed post_content (55 words, shortcodes/tags stripped) — the
// same helper artwork-list-item/render.php uses for its card text, reused
// here so "what shows in a card" and "what shows in a link preview" stay
// in sync. Trimmed further to a meta-description-appropriate length.
function cz_seo_excerpt($post) {
    $override = get_post_meta($post->ID, '_cz_seo_description', true);
    if ($override) {
        return $override;
    }
    $excerpt = wp_strip_all_tags(get_the_excerpt($post));
    return $excerpt ? wp_trim_words($excerpt, 30, '…') : get_option('cz_seo_default_description');
}

function cz_seo_current_url() {
    global $wp;
    return home_url('/' . ltrim($wp->request, '/'));
}

function cz_seo_get_context() {
    $context = [
        'title'       => wp_get_document_title(),
        'description' => get_option('cz_seo_default_description'),
        'url'         => cz_seo_current_url(),
        'type'        => 'website',
        'image'       => cz_seo_default_image(),
        'noindex'     => false,
    ];

    if (is_front_page()) {
        $tagline = get_bloginfo('description');
        if ($tagline) {
            $context['description'] = $tagline;
        }
        return $context;
    }

    if (is_singular()) {
        $post = get_queried_object();
        $context['type']        = in_array($post->post_type, ['post', 'artwork'], true) ? 'article' : 'website';
        $context['description'] = cz_seo_excerpt($post);

        $override_image_id = (int) get_post_meta($post->ID, '_cz_seo_image_id', true);
        $thumbnail = $override_image_id
            ? cz_seo_image_from_attachment($override_image_id)
            : (has_post_thumbnail($post) ? cz_seo_image_from_attachment(get_post_thumbnail_id($post)) : null);
        if ($thumbnail) {
            $context['image'] = $thumbnail;
        }
        return $context;
    }

    if (is_tax('collection')) {
        $term        = get_queried_object();
        $description = term_description($term);
        $context['description'] = $description
            ? wp_trim_words(wp_strip_all_tags($description), 30, '…')
            : sprintf('Kollektion „%s“ von Claudia Zemp.', $term->name);
        $thumbnail_id = (int) get_term_meta($term->term_id, 'thumbnail_id', true);
        if ($thumbnail_id) {
            $thumbnail = cz_seo_image_from_attachment($thumbnail_id);
            if ($thumbnail) {
                $context['image'] = $thumbnail;
            }
        }
        return $context;
    }

    if (is_search() || is_404()) {
        // Nothing meaningful to show a crawler or a link preview for —
        // and no reason to let either get indexed.
        $context['noindex'] = true;
        return $context;
    }

    return $context;
}

add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }

    $context = cz_seo_get_context();

    echo "\n<!-- SEO / Open Graph -->\n";

    if ($context['noindex']) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }

    if ($context['description']) {
        printf('<meta name="description" content="%s">' . "\n", esc_attr($context['description']));
    }

    printf('<meta property="og:locale" content="%s">' . "\n", esc_attr(str_replace('-', '_', get_locale())));
    printf('<meta property="og:type" content="%s">' . "\n", esc_attr($context['type']));
    printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr(get_bloginfo('name')));
    printf('<meta property="og:title" content="%s">' . "\n", esc_attr($context['title']));
    printf('<meta property="og:url" content="%s">' . "\n", esc_url($context['url']));
    if ($context['description']) {
        printf('<meta property="og:description" content="%s">' . "\n", esc_attr($context['description']));
    }
    if ($context['image']) {
        printf('<meta property="og:image" content="%s">' . "\n", esc_url($context['image']['url']));
        printf('<meta property="og:image:width" content="%d">' . "\n", (int) $context['image']['width']);
        printf('<meta property="og:image:height" content="%d">' . "\n", (int) $context['image']['height']);
        printf('<meta property="og:image:alt" content="%s">' . "\n", esc_attr($context['title']));
    }

    printf('<meta name="twitter:card" content="%s">' . "\n", $context['image'] ? 'summary_large_image' : 'summary');
    printf('<meta name="twitter:title" content="%s">' . "\n", esc_attr($context['title']));
    if ($context['description']) {
        printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($context['description']));
    }
    if ($context['image']) {
        printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($context['image']['url']));
    }
}, 1);
