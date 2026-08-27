// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { RichText, useBlockProps } from '@wordpress/block-editor';
import { renderAnimationIcon } from './animations';

export default function save({ attributes }) {
	const { text, url, linkTarget, rel, animation } = attributes;

	const blockProps = useBlockProps.save({
		className: 'wp-block-button__link wp-element-button',
	});

	return (
		<a
			{...blockProps}
			href={url || undefined}
			target={'_self' !== linkTarget ? linkTarget : undefined}
			rel={rel || undefined}
		>
			<RichText.Content tagName="span" className="cz-animated-button__label" value={text} />
			{renderAnimationIcon(animation)}
		</a>
	);
}
