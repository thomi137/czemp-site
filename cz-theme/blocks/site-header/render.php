<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Fully server-rendered: no `save()`, so there's nothing for the block
// editor to validate the stored markup against. The logo/site-title
// layout lives here instead of as hand-typed static block markup, which
// kept drifting out of sync with what WordPress's own block-supports
// system expects (see git history on patterns/header.php for the saga).

$wrapper = get_block_wrapper_attributes(['class' => 'cz-header']);
?>
<header <?php echo $wrapper; ?>>
    <div class="cz-header__brand">
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <img
                src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/cz_Logo_without_text.webp'); ?>"
                alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                width="72"
            />
        </a>
        <?php
        // Rendered via do_blocks() so the site title stays a real, dynamic
        // core/site-title block (reflects Settings > General, themeable
        // like any other block) without being subject to static-content
        // validation itself.
        echo do_blocks(
            '<!-- wp:site-title {"style":{"typography":{"fontWeight":"300","letterSpacing":"0.08em","textTransform":"uppercase"},"elements":{"link":{"color":{"text":"#000000"},":hover":{"color":{"text":"#000000"}}}}}} /-->'
        );
        ?>
    </div>
</header>
