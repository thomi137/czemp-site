// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// "Sekundärbild" panel in the Werk edit screen sidebar, editing the
// secondary_image/_focal_x/_focal_y post meta directly (inc/post-types.php
// registers them with show_in_rest, so they're already exposed on the
// editor's meta store — this just gives Claudia a picker for them). Same
// MediaUpload/FocalPointPicker wiring as blocks/gallery-item/edit.js, just
// pointed at core/editor meta dispatch instead of block setAttributes.
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.plugins || ! wp.data || ! wp.element || ! wp.components || ! wp.blockEditor ) {
		return;
	}

	var PluginDocumentSettingPanel =
		( wp.editor && wp.editor.PluginDocumentSettingPanel ) ||
		( wp.editPost && wp.editPost.PluginDocumentSettingPanel );

	if ( ! PluginDocumentSettingPanel ) {
		return;
	}

	var el = wp.element.createElement;
	var Button = wp.components.Button;
	var FocalPointPicker = wp.components.FocalPointPicker;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var withSelect = wp.data.withSelect;
	var withDispatch = wp.data.withDispatch;

	function SekundaerbildPanel( props ) {
		var imageId = props.imageId || 0;
		var imageUrl = props.imageUrl || '';
		var focalPoint = { x: props.focalX, y: props.focalY };

		var children = [
			el( MediaUploadCheck, { key: 'upload' },
				el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: imageId,
					onSelect: function ( media ) {
						props.setImage( media.id, media.url );
					},
					render: function ( openProps ) {
						return el( Button, {
							variant: 'secondary',
							onClick: openProps.open,
						}, imageUrl ? 'Bild ersetzen' : 'Bild auswählen' );
					},
				} )
			),
		];

		if ( imageUrl ) {
			children.push(
				el( FocalPointPicker, {
					key: 'focal-point',
					url: imageUrl,
					value: focalPoint,
					onChange: function ( value ) {
						props.setFocalPoint( value.x, value.y );
					},
				} )
			);
			children.push(
				el( Button, {
					key: 'remove',
					variant: 'tertiary',
					isDestructive: true,
					onClick: props.removeImage,
				}, 'Entfernen' )
			);
		}

		return el(
			PluginDocumentSettingPanel,
			{ name: 'cz-artwork-secondary-image', title: 'Sekundärbild' },
			children
		);
	}

	var SekundaerbildPanelWithData = withDispatch( function ( dispatch ) {
		return {
			setImage: function ( id, url ) {
				dispatch( 'core/editor' ).editPost( { meta: { secondary_image: id } } );
			},
			setFocalPoint: function ( x, y ) {
				dispatch( 'core/editor' ).editPost( {
					meta: { secondary_image_focal_x: x, secondary_image_focal_y: y },
				} );
			},
			removeImage: function () {
				dispatch( 'core/editor' ).editPost( {
					meta: {
						secondary_image: 0,
						secondary_image_focal_x: 0.5,
						secondary_image_focal_y: 0.5,
					},
				} );
			},
		};
	} )( SekundaerbildPanel );

	SekundaerbildPanelWithData = withSelect( function ( select ) {
		var meta = select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		var imageId = meta.secondary_image || 0;
		var media = imageId ? select( 'core' ).getMedia( imageId ) : null;

		return {
			imageId: imageId,
			imageUrl: media ? media.source_url : '',
			focalX: typeof meta.secondary_image_focal_x === 'number' ? meta.secondary_image_focal_x : 0.5,
			focalY: typeof meta.secondary_image_focal_y === 'number' ? meta.secondary_image_focal_y : 0.5,
		};
	} )( SekundaerbildPanelWithData );

	wp.plugins.registerPlugin( 'cz-artwork-secondary-image-panel', {
		render: function () {
			return el( SekundaerbildPanelWithData );
		},
	} );
} )( window.wp );
