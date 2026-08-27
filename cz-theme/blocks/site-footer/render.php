<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Fully server-rendered, same reasoning as czemp-theme/site-header: no
// save() means nothing for the editor to invalidate against.

// Looked up by slug rather than hard-coding a wp_navigation post ID —
// that ID differs per environment (local vs. live) and would silently
// break if the menu were ever recreated. Slug rather than title: WP
// auto-generates the slug from the title once at creation
// (sanitize_title('Footer Menu') === 'footer-menu', confirmed against
// production) and it isn't exposed for editing anywhere in the normal
// Navigation UI the way the title is — an Editor renaming "Footer Menu"
// to something else (an easy, innocent edit) would silently break a
// title-based lookup but leaves a slug-based one alone.
function cz_get_footer_nav_id() {
    static $id = null;
    if ($id !== null) {
        return $id;
    }
    $posts = get_posts([
        'post_type'      => 'wp_navigation',
        'name'           => 'footer-menu',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ]);
    $id = $posts ? $posts[0]->ID : 0;
    return $id;
}

$nav_id = cz_get_footer_nav_id();

// No wrapping element here on purpose: czemp-theme/sticky-nav relies on
// `position: sticky; bottom: 0` on its own root element, which only
// sticks correctly against .wp-site-blocks (its containing block) when
// it's a *direct* child of it. Wrapping it one level deeper in a shared
// footer <div> shrank its containing block down to just this block's own
// content height and broke the stickiness.
if ($nav_id) :
    // Menü-Stil preview switch (Settings -> Menü-Stil, inc/nav-style.php)
    // - passed through explicitly since this is the only place
    // czemp-theme/sticky-nav ever gets instantiated.
    $nav_button_style = get_option('cz_nav_button_style') ? 'true' : 'false';
    echo do_blocks(
        '<!-- wp:czemp-theme/sticky-nav {"buttonStyle":' . $nav_button_style . '} -->' .
        '<!-- wp:navigation {"ref":' . (int) $nav_id . ',"overlayMenu":"never","layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"blockGap":"32px"},"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontSize":"18px"}},"textColor":"white"} /-->' .
        '<!-- /wp:czemp-theme/sticky-nav -->'
    );
endif;
?>
<footer class="cz-site-footer__bar">
    <p>© <?php echo esc_html(date('Y')); ?> Claudia Zemp</p>
    <p class="cz-site-footer__legal">
        <a href="<?php echo esc_url(home_url('/impressum/')); ?>">Impressum</a>
        · <a href="<?php echo esc_url(home_url('/datenschutz/')); ?>">Datenschutz</a>
    </p>
</footer>
