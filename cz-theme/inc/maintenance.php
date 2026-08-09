<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

/**
 * Simple theme-level maintenance/"coming soon" mode.
 *
 * Replaces the SeedProd plugin (deactivated — it wasn't actually doing
 * anything, and its empty "Meta-Boxen" editor panel was confusing
 * Claudia). Off by default; toggled from its own Settings page. That page
 * requires manage_options — the same capability Claudia's Editor role
 * deliberately doesn't have (see inc/admin.php) — so this is Thomas-only,
 * same as Plugins/Settings in general.
 *
 * Logged-in Administrators keep seeing the real site everywhere so they
 * can keep working while it's "closed" to visitors.
 */

add_action('admin_menu', function () {
    $title = 'Wartungsmodus';
    add_options_page($title, $title, 'manage_options', 'cz-maintenance', 'cz_render_maintenance_settings_page');
});

add_action('admin_init', function () {
    register_setting('cz_maintenance', 'cz_maintenance_mode', ['type' => 'boolean', 'default' => false]);
    register_setting('cz_maintenance', 'cz_maintenance_page_id', ['type' => 'integer', 'default' => 0]);
});

function cz_render_maintenance_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Wartungsmodus</h1>
        <form method="post" action="options.php">
            <?php settings_fields('cz_maintenance'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Website sperren</th>
                    <td>
                        <label>
                            <input type="checkbox" name="cz_maintenance_mode" value="1" <?php checked(get_option('cz_maintenance_mode')); ?>>
                            Besuchern statt der Website eine Wartungsseite anzeigen
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Wartungsseite</th>
                    <td>
                        <?php
                        wp_dropdown_pages([
                            'name'              => 'cz_maintenance_page_id',
                            'selected'          => (int) get_option('cz_maintenance_page_id'),
                            'show_option_none'  => '— Seite wählen —',
                            'option_none_value' => 0,
                        ]);
                        ?>
                        <p class="description">
                            Diese Seite wird angezeigt, solange der Wartungsmodus aktiv ist — ganz normal
                            im Editor bearbeitbar. Angemeldete Administratoren sehen weiterhin die normale
                            Website.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Speichern'); ?>
        </form>
    </div>
    <?php
}

add_action('template_redirect', function () {
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    if (!get_option('cz_maintenance_mode')) {
        return;
    }
    if (current_user_can('manage_options')) {
        return;
    }

    $page_id = (int) get_option('cz_maintenance_page_id');
    $page    = $page_id ? get_post($page_id) : null;

    if (!$page || $page->post_status !== 'publish') {
        return; // Not configured — fail open instead of locking out the whole site.
    }

    // 503 + Retry-After tells search engines this is temporary, so they
    // don't drop indexed pages just because maintenance mode was on for a
    // few hours.
    status_header(503);
    header('Retry-After: 3600');
    nocache_headers();

    // Swap the already-run main query's result for the maintenance page,
    // then let template-loader.php continue as normal — it'll resolve
    // page-{$page->post_name}.html if one exists, or fall back to the
    // generic page.html, exactly as if that page's own URL had been
    // requested directly.
    global $wp_query, $post;
    $post = $page;
    setup_postdata($post);

    $wp_query->queried_object    = $page;
    $wp_query->queried_object_id = $page->ID;
    $wp_query->post              = $page;
    $wp_query->posts             = [$page];
    $wp_query->post_count        = 1;
    $wp_query->found_posts       = 1;
    $wp_query->max_num_pages     = 1;
    $wp_query->is_page           = true;
    $wp_query->is_singular       = true;
    $wp_query->is_single         = false;
    $wp_query->is_front_page     = false;
    $wp_query->is_home           = false;
    $wp_query->is_404            = false;
    $wp_query->is_archive        = false;
    $wp_query->is_search         = false;
    $wp_query->set('pagename', $page->post_name);
});
