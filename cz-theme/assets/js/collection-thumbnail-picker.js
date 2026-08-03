// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

( function ( $ ) {
    'use strict';

    let frame;
    let dragging = false;

    function renderPreview( url, focalX, focalY ) {
        $( '#cz-collection-thumbnail-preview' ).html(
            '<div class="cz-focal-point-picker">' +
                '<img src="' + url + '" alt="">' +
                '<div class="cz-focal-point-marker" style="left:' + ( focalX * 100 ) + '%;top:' + ( focalY * 100 ) + '%;"></div>' +
            '</div>'
        );
    }

    function openPicker( e ) {
        e.preventDefault();

        if ( frame ) {
            frame.open();
            return;
        }

        frame = wp.media( {
            title: 'Vorschaubild auswählen',
            button: { text: 'Übernehmen' },
            multiple: false,
        } );

        frame.on( 'select', function () {
            const attachment = frame.state().get( 'selection' ).first().toJSON();
            const url = attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;

            $( '#cz-collection-thumbnail-id' ).val( attachment.id );
            $( '#cz-collection-thumbnail-focal-x' ).val( '0.5' );
            $( '#cz-collection-thumbnail-focal-y' ).val( '0.5' );
            renderPreview( url, 0.5, 0.5 );
            $( '#cz-collection-thumbnail-remove' ).show();
        } );

        frame.open();
    }

    function removeThumbnail( e ) {
        e.preventDefault();
        $( '#cz-collection-thumbnail-id' ).val( '' );
        $( '#cz-collection-thumbnail-focal-x' ).val( '0.5' );
        $( '#cz-collection-thumbnail-focal-y' ).val( '0.5' );
        $( '#cz-collection-thumbnail-preview' ).empty();
        $( '#cz-collection-thumbnail-remove' ).hide();
    }

    function focalPointFromEvent( e, picker ) {
        const rect = picker.getBoundingClientRect();
        const x = Math.min( 1, Math.max( 0, ( e.clientX - rect.left ) / rect.width ) );
        const y = Math.min( 1, Math.max( 0, ( e.clientY - rect.top ) / rect.height ) );
        return { x, y };
    }

    function setFocalPoint( x, y ) {
        $( '#cz-collection-thumbnail-focal-x' ).val( x.toFixed( 4 ) );
        $( '#cz-collection-thumbnail-focal-y' ).val( y.toFixed( 4 ) );
        $( '.cz-focal-point-marker' ).css( { left: ( x * 100 ) + '%', top: ( y * 100 ) + '%' } );
    }

    $( document ).on( 'click', '#cz-collection-thumbnail-button', openPicker );
    $( document ).on( 'click', '#cz-collection-thumbnail-remove', removeThumbnail );

    $( document ).on( 'pointerdown', '.cz-focal-point-picker', function ( e ) {
        dragging = true;
        const point = focalPointFromEvent( e, this );
        setFocalPoint( point.x, point.y );
    } );

    $( document ).on( 'pointermove', function ( e ) {
        if ( !dragging ) {
            return;
        }
        const picker = document.querySelector( '.cz-focal-point-picker' );
        if ( !picker ) {
            return;
        }
        const point = focalPointFromEvent( e, picker );
        setFocalPoint( point.x, point.y );
    } );

    $( document ).on( 'pointerup', function () {
        dragging = false;
    } );

} )( jQuery );
