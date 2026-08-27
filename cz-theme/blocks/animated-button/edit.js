// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	URLInput,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl, SelectControl } from '@wordpress/components';
import { ANIMATION_OPTIONS, renderAnimationIcon } from './animations';

export default function Edit({ attributes, setAttributes }) {
	const { text, url, linkTarget, rel, animation } = attributes;

	// No wrapping <div> — the <a> itself is the block's root element, so
	// padding/font-size/colour/border-radius supports apply directly to it
	// with no get_block_wrapper_attributes()/selectors indirection needed
	// (unlike current-exhibitions, which has to re-target an inner
	// <button> because its wrapper is a separate outer <div>). The label
	// is a nested <span> (not the <a> itself) so the icon can sit next to
	// it as a sibling — same split current-exhibitions uses internally.
	const blockProps = useBlockProps({
		className: 'wp-block-button__link wp-element-button',
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Link', 'czemp-theme')}>
					<URLInput
						value={url}
						onChange={(newUrl) => setAttributes({ url: newUrl })}
					/>
					<ToggleControl
						label={__('In neuem Tab öffnen', 'czemp-theme')}
						checked={'_blank' === linkTarget}
						onChange={(value) =>
							setAttributes({ linkTarget: value ? '_blank' : '_self' })
						}
					/>
					<TextControl
						label={__('rel-Attribut', 'czemp-theme')}
						value={rel}
						onChange={(newRel) => setAttributes({ rel: newRel })}
					/>
				</PanelBody>
				<PanelBody title={__('Animation', 'czemp-theme')}>
					<SelectControl
						label={__('Scroll-Animation', 'czemp-theme')}
						value={animation}
						options={ANIMATION_OPTIONS}
						onChange={(value) => setAttributes({ animation: value })}
					/>
				</PanelBody>
			</InspectorControls>
			<a {...blockProps} href={url || undefined}>
				<RichText
					tagName="span"
					className="cz-animated-button__label"
					value={text}
					onChange={(newText) => setAttributes({ text: newText })}
					placeholder={__('Button-Text', 'czemp-theme')}
					allowedFormats={[]}
				/>
				{renderAnimationIcon(animation)}
			</a>
		</>
	);
}
