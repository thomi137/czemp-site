<?php

$post_id = $block->context['postId'];

if ( ! $post_id ) {
    return;
}

$show_excerpt = $attributes['showExcerpt'] ?? true;
$show_image   = $attributes['showImage'] ?? true;
$image_size   = $attributes['imageSize'] ?? 120;

?>

<div <?php echo get_block_wrapper_attributes([
    'class' => 'post-list-item'
]); ?>>

    <?php if ( $show_image && has_post_thumbnail( $post_id ) ) : ?>

        <a
            href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"
            class="post-list-item__image-link"
        >

            <?php
            echo get_the_post_thumbnail(
                $post_id,
                'medium',
                [
                    'class' => 'post-list-item__image',
                    'style' => sprintf(
                        'width:%dpx;height:%dpx;',
                        $image_size,
                        $image_size
                    )
                ]
            );
            ?>

        </a>

    <?php endif; ?>

    <div class="post-list-item__content">

        <h3 class="post-list-item__title">
            <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
                <?php echo esc_html( get_the_title( $post_id ) ); ?>
            </a>
        </h3>

        <?php if ( $show_excerpt ) : ?>

            <div class="post-list-item__excerpt">
                <?php echo esc_html( get_the_excerpt( $post_id ) ); ?>
            </div>

        <?php endif; ?>

    </div>

</div>
