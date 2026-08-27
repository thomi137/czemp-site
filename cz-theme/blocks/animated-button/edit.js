// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	URLInput,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl, SelectControl } from '@wordpress/components';
import { getBlockType } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';
import { ANIMATION_OPTIONS, renderAnimationIcon } from './animations';

export default function Edit({ attributes, setAttributes }) {
	const { text, url, linkTarget, rel, animation, linkType, targetAnchor } = attributes;

	// Every top-level block on the page that has an HTML Anchor set, in
	// document order — same contract current-exhibitions's anchor picker
	// uses (see its edit.js), so "anchor" mode here resolves identically.
	const anchoredBlocks = useSelect(
		(select) => select('core/block-editor').getBlocks().filter((block) => block.attributes?.anchor),
		[]
	);
	const anchorOptions = [
		{ value: '', label: __('Automatisch: nächster verankerter Abschnitt', 'czemp-theme') },
		...anchoredBlocks.map((block) => ({
			value: block.attributes.anchor,
			label: `${block.attributes.anchor} (${getBlockType(block.name)?.title ?? block.name})`,
		})),
	];

	// No wrapping <div> — the root element itself (<a> in "url" mode,
	// <button> in "anchor" mode) is the block's root, so
	// padding/font-size/colour/border-radius supports apply directly to
	// it with no get_block_wrapper_attributes()/selectors indirection
	// needed (unlike current-exhibitions, which has to re-target an inner
	// <button> because its wrapper is a separate outer <div>). The label
	// is a nested <span> (not the root itself) so the icon can sit next
	// to it as a sibling — same split current-exhibitions uses
	// internally.
	const blockProps = useBlockProps({
		className: 'wp-block-button__link wp-element-button',
	});

	const label = (
		<RichText
			tagName="span"
			className="cz-animated-button__label"
			value={text}
			onChange={(newText) => setAttributes({ text: newText })}
			placeholder={__('Button-Text', 'czemp-theme')}
			allowedFormats={[]}
		/>
	);
	const icon = renderAnimationIcon(animation);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Link', 'czemp-theme')}>
					<SelectControl
						label={__('Link-Typ', 'czemp-theme')}
						value={linkType}
						options={[
							{ value: 'url', label: __('URL', 'czemp-theme') },
							{ value: 'anchor', label: __('Sprungziel', 'czemp-theme') },
						]}
						onChange={(value) => setAttributes({ linkType: value })}
					/>
					{'anchor' === linkType ? (
						<SelectControl
							label={__('Abschnitt, zu dem gescrollt wird', 'czemp-theme')}
							value={targetAnchor}
							options={anchorOptions}
							onChange={(value) => setAttributes({ targetAnchor: value })}
							help={__(
								'Ohne Auswahl springt der Button automatisch zum nächsten Abschnitt mit gesetztem HTML-Anker.',
								'czemp-theme'
							)}
						/>
					) : (
						<>
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
						</>
					)}
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
			{'anchor' === linkType ? (
				<button type="button" {...blockProps}>
					{label}
					{icon}
				</button>
			) : (
				<a {...blockProps} href={url || undefined}>
					{label}
					{icon}
				</a>
			)}
		</>
	);
}
