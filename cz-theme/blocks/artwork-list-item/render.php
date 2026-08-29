<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

$post_id = $block->context['postId'];

if ( ! $post_id ) {
    return;
}

$show_excerpt = $attributes['showExcerpt'] ?? true;
$show_image   = $attributes['showImage'] ?? true;

// Stamp which Kollektion this link came from onto the artwork's own URL,
// so artwork-nav (single-artwork.html) knows to keep prev/next inside
// this same collection instead of falling back to the artwork's first
// assigned term. Only meaningful on a collection archive - every
// /kollektion/.../ page is already scoped to exactly one term (see
// inc/frontend.php), so get_queried_object_id() is unambiguous here.
$permalink = get_permalink( $post_id );
if ( is_tax( 'collection' ) ) {
    $permalink = add_query_arg( 'kollektion', get_queried_object_id(), $permalink );
}

?>
<div <?php echo get_block_wrapper_attributes(['class' => 'post-list-item']); ?>>

    <?php if ( $show_image && has_post_thumbnail( $post_id ) ) : ?>
        <a href="<?php echo esc_url( $permalink ); ?>" class="post-list-item__image-link">
            <?php echo get_the_post_thumbnail( $post_id, 'large', ['class' => 'post-list-item__image'] ); ?>
        </a>
    <?php endif; ?>

    <div class="post-list-item__content">
        <h3 class="post-list-item__title">
            <a href="<?php echo esc_url( $permalink ); ?>">
                <?php echo esc_html( get_the_title( $post_id ) ); ?>
            </a>
        </h3>
        <?php
        $price = ( get_post_type( $post_id ) === 'artwork' ) ? get_post_meta( $post_id, 'price', true ) : '';
        if ( $price !== '' ) :
        ?>
            <p class="post-list-item__price"><?php echo esc_html( $price ); ?></p>
        <?php endif; ?>
        <?php if ( $show_excerpt ) : ?>
            <p class="post-list-item__excerpt"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
        <?php endif; ?>
    </div>

</div>
