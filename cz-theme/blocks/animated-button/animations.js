// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { __ } from '@wordpress/i18n';

// Single source for the dropdown options AND the saved markup, shared by
// edit.js and save.js — the two can never drift apart on what value maps
// to what icon, since neither hand-repeats this list.
export const ANIMATION_OPTIONS = [
	{ value: 'none', label: __('(kein)', 'czemp-theme') },
	{ value: 'chevron-track', label: __('Verfolgende Chevrons', 'czemp-theme') },
	{ value: 'bounce-arrow', label: __('Auf-Ab-Pfeil', 'czemp-theme') },
	{ value: 'pulse-dot', label: __('Pulsierender Punkt', 'czemp-theme') },
	{ value: 'slide-arrow', label: __('Gleitender Pfeil', 'czemp-theme') },
];

const ARROW_POINTS = '6 10 12 16 18 10';

// Icon markup per animation choice. The actual animating happens in
// style.css, keyed off these exact class names — this only decides which
// icon shape shows up for which choice. 'none' renders nothing, so a
// plain button stays exactly as plain as a real core/button instance,
// same as current-exhibitions renders no icon when there's nothing to
// show.
export function renderAnimationIcon(animation) {
	switch (animation) {
		case 'chevron-track':
			return (
				<svg
					className="cz-animated-button__icon cz-animated-button__icon--chevron-track"
					viewBox="0 0 24 26"
					width="16"
					height="18"
					fill="none"
					stroke="currentColor"
					strokeWidth="2.5"
					strokeLinecap="round"
					strokeLinejoin="round"
					aria-hidden="true"
				>
					<polyline className="cz-animated-button__chevron cz-animated-button__chevron--top" points="6 5 12 10 18 5" />
					<polyline className="cz-animated-button__chevron cz-animated-button__chevron--middle" points="6 12 12 17 18 12" />
					<polyline className="cz-animated-button__chevron cz-animated-button__chevron--bottom" points="6 19 12 24 18 19" />
				</svg>
			);
		case 'bounce-arrow':
			return (
				<svg
					className="cz-animated-button__icon cz-animated-button__icon--bounce-arrow"
					viewBox="0 0 24 24"
					width="16"
					height="16"
					fill="none"
					stroke="currentColor"
					strokeWidth="2.5"
					strokeLinecap="round"
					strokeLinejoin="round"
					aria-hidden="true"
				>
					<polyline points={ARROW_POINTS} />
				</svg>
			);
		case 'slide-arrow':
			return (
				<svg
					className="cz-animated-button__icon cz-animated-button__icon--slide-arrow"
					viewBox="0 0 24 24"
					width="16"
					height="16"
					fill="none"
					stroke="currentColor"
					strokeWidth="2.5"
					strokeLinecap="round"
					strokeLinejoin="round"
					aria-hidden="true"
				>
					<polyline points={ARROW_POINTS} />
				</svg>
			);
		case 'pulse-dot':
			return (
				<svg
					className="cz-animated-button__icon cz-animated-button__icon--pulse-dot"
					viewBox="0 0 16 16"
					width="10"
					height="10"
					aria-hidden="true"
				>
					<circle cx="8" cy="8" r="6" fill="currentColor" />
				</svg>
			);
		case 'none':
		default:
			return null;
	}
}
