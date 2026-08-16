// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

( function ( $ ) {
    'use strict';

    if ( typeof czSortable === 'undefined' || !czSortable.termId || !czSortable.orderIsDefault ) {
        return;
    }

    const $list = $( '#the-list' );

    function currentOrder() {
        return $list.children( 'tr' ).map( function () {
            return parseInt( this.id.replace( 'post-', '' ), 10 );
        } ).get();
    }

    function showSaveError() {
        $( '<div class="notice notice-error is-dismissible"><p>' +
            'Reihenfolge konnte nicht gespeichert werden. Die Seite wird neu geladen.' +
            '</p></div>' ).insertAfter( '.wp-header-end' );
        setTimeout( function () {
            window.location.reload();
        }, 2500 );
    }

    $list.sortable( {
        items: '> tr',
        handle: '.cz-drag-handle',
        axis: 'y',
        cursor: 'grabbing',
        update: function () {
            const order = currentOrder();
            $list.sortable( 'disable' );

            $.ajax( {
                url: czSortable.restUrl,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify( { term_id: czSortable.termId, order: order } ),
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', czSortable.nonce );
                },
            } ).done( function () {
                $list.sortable( 'enable' );
            } ).fail( function () {
                showSaveError();
            } );
        },
    } );

} )( jQuery );
