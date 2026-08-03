<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

$term = get_queried_object();

if ( ! ( $term instanceof WP_Term ) || $term->taxonomy !== 'collection' ) {
    return;
}

$children = get_terms( [
    'taxonomy'   => 'collection',
    'parent'     => $term->term_id,
    'hide_empty' => false,
] );

if ( is_wp_error( $children ) || empty( $children ) ) {
    return;
}

?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'cz-subcollection-grid' ] ); ?>>
    <?php foreach ( $children as $child ) :
        $thumbnail_id  = (int) get_term_meta( $child->term_id, 'thumbnail_id', true );
        $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'large' ) : '';

        $focal_x = get_term_meta( $child->term_id, 'thumbnail_focal_x', true );
        $focal_y = get_term_meta( $child->term_id, 'thumbnail_focal_y', true );
        $focal_x = ( $focal_x === '' ) ? 0.5 : (float) $focal_x;
        $focal_y = ( $focal_y === '' ) ? 0.5 : (float) $focal_y;
        ?>
        <a href="<?php echo esc_url( get_term_link( $child ) ); ?>">
            <div class="gallery-item">
                <?php if ( $thumbnail_url ) : ?>
                    <img
                        src="<?php echo esc_url( $thumbnail_url ); ?>"
                        alt="<?php echo esc_attr( $child->name ); ?>"
                        style="object-position: <?php echo esc_attr( $focal_x * 100 ); ?>% <?php echo esc_attr( $focal_y * 100 ); ?>%;"
                    >
                <?php endif; ?>
                <div class="overlay">
                    <h3><?php echo esc_html( $child->name ); ?></h3>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
