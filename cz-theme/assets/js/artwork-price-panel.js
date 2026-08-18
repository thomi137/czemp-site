// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// "Preis" panel in the Werk edit screen sidebar, editing the `price` post
// meta directly (inc/post-types.php registers it with show_in_rest, so
// it's already exposed on the editor's meta store — this just gives
// Claudia a field to see/change it in). The artwork-price block that
// *displays* this value lives in the single-artwork FSE template, which
// her Editor account can't open (see inc/admin.php) — this panel is the
// only place she can actually set it.
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.plugins || ! wp.data || ! wp.element || ! wp.components ) {
		return;
	}

	var PluginDocumentSettingPanel =
		( wp.editor && wp.editor.PluginDocumentSettingPanel ) ||
		( wp.editPost && wp.editPost.PluginDocumentSettingPanel );

	if ( ! PluginDocumentSettingPanel ) {
		return;
	}

	var el = wp.element.createElement;
	var TextControl = wp.components.TextControl;
	var withSelect = wp.data.withSelect;
	var withDispatch = wp.data.withDispatch;

	function PreisPanel( props ) {
		return el(
			PluginDocumentSettingPanel,
			{ name: 'cz-artwork-price', title: 'Preis' },
			el( TextControl, {
				label: 'Preis',
				value: props.price || '',
				onChange: props.setPrice,
				help: 'z.B. "Fr. 780.-"',
			} )
		);
	}

	var PreisPanelWithData = withDispatch( function ( dispatch ) {
		return {
			setPrice: function ( price ) {
				dispatch( 'core/editor' ).editPost( { meta: { price: price } } );
			},
		};
	} )( PreisPanel );

	PreisPanelWithData = withSelect( function ( select ) {
		var meta = select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		return { price: meta.price };
	} )( PreisPanelWithData );

	wp.plugins.registerPlugin( 'cz-artwork-price-panel', {
		render: function () {
			return el( PreisPanelWithData );
		},
	} );
} )( window.wp );
