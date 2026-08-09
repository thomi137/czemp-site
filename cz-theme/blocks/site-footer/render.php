<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Fully server-rendered, same reasoning as czemp-theme/site-header: no
// save() means nothing for the editor to invalidate against.

// Looked up by title rather than hard-coding a wp_navigation post ID —
// that ID differs per environment (local vs. live) and would silently
// break if the menu were ever recreated.
function cz_get_footer_nav_id() {
    static $id = null;
    if ($id !== null) {
        return $id;
    }
    $posts = get_posts([
        'post_type'      => 'wp_navigation',
        'title'          => 'Footer Menu',
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
    echo do_blocks(
        '<!-- wp:czemp-theme/sticky-nav -->' .
        '<!-- wp:navigation {"ref":' . (int) $nav_id . ',"overlayMenu":"never","layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"blockGap":"32px"},"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontSize":"18px"}},"textColor":"white"} /-->' .
        '<!-- /wp:czemp-theme/sticky-nav -->'
    );
endif;
?>
<footer class="cz-site-footer__bar">
    <p>© <?php echo esc_html(date('Y')); ?> Claudia Zemp</p>
</footer>
