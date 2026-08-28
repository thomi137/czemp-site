<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

/**
 * Menü-Stil: a preview switch for the sticky-nav links, so Claudia can
 * actually see the "pillbox/transparent" look (same recipe the site's
 * pagination links already use) on the live site before deciding on it,
 * rather than judging it from a description.
 *
 * Off by default; toggled from its own Settings page, same manage_options
 * gate as Wartungsmodus (inc/maintenance.php) - Thomas-only, since
 * czemp-theme/sticky-nav is only ever instantiated from a hardcoded
 * string in site-footer/render.php (never a real, independently editable
 * block instance), so there's no Site Editor surface to put this toggle
 * on even if Claudia's role allowed it.
 */

add_action('admin_menu', function () {
    $title = 'Menü-Stil';
    add_options_page($title, $title, 'manage_options', 'cz-nav-style', 'cz_render_nav_style_settings_page');
});

add_action('admin_init', function () {
    register_setting('cz_nav_style', 'cz_nav_button_style', ['type' => 'boolean', 'default' => false]);
});

function cz_render_nav_style_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Menü-Stil</h1>
        <form method="post" action="options.php">
            <?php settings_fields('cz_nav_style'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Sticky-Menü</th>
                    <td>
                        <label>
                            <input type="checkbox" name="cz_nav_button_style" value="1" <?php checked(get_option('cz_nav_button_style')); ?>>
                            Menüpunkte (Desktop-Leiste und mobiles Menü) als transparente Pillen (wie bei der Seitennummerierung) statt als Text anzeigen
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button('Speichern'); ?>
        </form>
    </div>
    <?php
}
