// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { RichText, useBlockProps } from '@wordpress/block-editor';
import { renderAnimationIcon } from './animations';

export default function save({ attributes }) {
	const { text, url, linkTarget, rel, animation, linkType, targetAnchor } = attributes;

	const blockProps = useBlockProps.save({
		className: 'wp-block-button__link wp-element-button',
	});

	const label = <RichText.Content tagName="span" className="cz-animated-button__label" value={text} />;
	const icon = renderAnimationIcon(animation);

	if ('anchor' === linkType) {
		// No href at all — view.js's click handler does the scrolling.
		// data-target-anchor is only present for an explicit pick; ''
		// (automatic next-anchor) omits it entirely, same contract
		// current-exhibitions/render.php uses.
		return (
			<button
				type="button"
				{...blockProps}
				data-target-anchor={targetAnchor || undefined}
			>
				{label}
				{icon}
			</button>
		);
	}

	return (
		<a
			{...blockProps}
			href={url || undefined}
			target={'_self' !== linkTarget ? linkTarget : undefined}
			rel={rel || undefined}
		>
			{label}
			{icon}
		</a>
	);
}
