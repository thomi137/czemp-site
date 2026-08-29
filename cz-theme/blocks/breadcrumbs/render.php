<?php
// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

function cz_breadcrumb_gallery_link() {
    $page = get_page_by_path( 'gallery' );
    return $page ? get_permalink( $page ) : home_url( '/gallery/' );
}

$crumbs = [
    [ 'label' => 'Home', 'url' => home_url( '/' ) ],
];

if ( is_singular( 'artwork' ) ) {
    $crumbs[] = [ 'label' => 'Galerie', 'url' => cz_breadcrumb_gallery_link() ];

    $terms = get_the_terms( get_the_ID(), 'collection' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        // Same context artwork-nav already tracks (see blocks/artwork-nav/
        // render.php): which Kollektion the visitor actually arrived from,
        // when known, instead of always the artwork's first assigned term.
        $requested_term_id = isset( $_GET['kollektion'] ) ? absint( $_GET['kollektion'] ) : 0;
        $has_context        = $requested_term_id && is_object_in_term( get_the_ID(), 'collection', [ $requested_term_id ] );
        $term               = $has_context ? get_term( $requested_term_id, 'collection' ) : $terms[0];
        if ( $term->parent ) {
            $parent = get_term( $term->parent, 'collection' );
            if ( $parent && ! is_wp_error( $parent ) ) {
                $crumbs[] = [ 'label' => $parent->name, 'url' => get_term_link( $parent ) ];
            }
        }
        $crumbs[] = [ 'label' => $term->name, 'url' => get_term_link( $term ) ];
    }

    $crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];

} elseif ( is_tax( 'collection' ) ) {
    $crumbs[] = [ 'label' => 'Galerie', 'url' => cz_breadcrumb_gallery_link() ];

    $term = get_queried_object();
    if ( $term instanceof WP_Term && $term->parent ) {
        $parent = get_term( $term->parent, 'collection' );
        if ( $parent && ! is_wp_error( $parent ) ) {
            $crumbs[] = [ 'label' => $parent->name, 'url' => get_term_link( $parent ) ];
        }
    }
    if ( $term instanceof WP_Term ) {
        $crumbs[] = [ 'label' => $term->name, 'url' => '' ];
    }

} elseif ( is_post_type_archive( 'artwork' ) ) {
    $crumbs[] = [ 'label' => 'Galerie', 'url' => cz_breadcrumb_gallery_link() ];
    $crumbs[] = [ 'label' => post_type_archive_title( '', false ), 'url' => '' ];

} else {
    return;
}

$last = count( $crumbs ) - 1;

?>
<nav <?php echo get_block_wrapper_attributes( [ 'class' => 'cz-breadcrumbs' ] ); ?> aria-label="Breadcrumb">
    <ol>
        <?php foreach ( $crumbs as $i => $crumb ) : ?>
            <li>
                <?php if ( $crumb['url'] && $i !== $last ) : ?>
                    <a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['label'] ); ?></a>
                <?php else : ?>
                    <span aria-current="page"><?php echo esc_html( $crumb['label'] ); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
