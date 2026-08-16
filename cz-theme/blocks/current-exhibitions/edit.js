// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { getBlockType } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const { heading, targetAnchor } = attributes;

	const blockProps = useBlockProps({ className: 'cz-current-exhibitions' });

	// Every top-level block on the page that has an HTML Anchor set, in
	// document order — mirrors the render-time contract (see
	// context/current-feature.md), which only ever resolves anchors on
	// top-level blocks. Root-level getBlocks() is the editor's equivalent
	// of ".entry-content"'s direct children.
	const anchoredBlocks = useSelect(
		(select) => select('core/block-editor').getBlocks().filter((block) => block.attributes?.anchor),
		[]
	);

	const anchorOptions = [
		{ value: '', label: __('Automatisch: nächster verankerter Abschnitt', 'czemp') },
		...anchoredBlocks.map((block) => ({
			value: block.attributes.anchor,
			label: `${block.attributes.anchor} (${getBlockType(block.name)?.title ?? block.name})`,
		})),
	];

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Sprungziel', 'czemp')}>
					<SelectControl
						label={__('Abschnitt, zu dem gescrollt wird', 'czemp')}
						value={targetAnchor}
						options={anchorOptions}
						onChange={(value) => setAttributes({ targetAnchor: value })}
						help={__(
							'Ohne Auswahl springt der Button automatisch zum nächsten Abschnitt mit gesetztem HTML-Anker.',
							'czemp'
						)}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				{/* One button: label and chevrons both live inside the same
				    <button>, same real button classes + inline style as
				    render.php's — see the comment there. Editor preview should
				    look identical to the front end, not an approximation of it. */}
				<button
					type="button"
					className="cz-current-exhibitions__arrow wp-block-button__link has-text-color has-background has-custom-font-size wp-element-button"
					style={{
						borderRadius: 0,
						color: '#ffffff',
						backgroundColor: '#1a1a1a',
						fontSize: '12px',
						letterSpacing: '0.1em',
						textTransform: 'uppercase',
					}}
				>
					<RichText
						tagName="span"
						className="cz-current-exhibitions__heading"
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
						allowedFormats={[]}
						placeholder={__('Aktuelle Ausstellungen', 'czemp')}
					/>
					<svg className="cz-current-exhibitions__icon" viewBox="0 0 24 26" width="16" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
						<polyline className="cz-current-exhibitions__chevron cz-current-exhibitions__chevron--top" points="6 5 12 10 18 5"></polyline>
						<polyline className="cz-current-exhibitions__chevron cz-current-exhibitions__chevron--middle" points="6 12 12 17 18 12"></polyline>
						<polyline className="cz-current-exhibitions__chevron cz-current-exhibitions__chevron--bottom" points="6 19 12 24 18 19"></polyline>
					</svg>
				</button>
			</div>
		</>
	);
}
