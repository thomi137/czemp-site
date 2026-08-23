// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Click/keyboard/swipe triggering moved to carousel.js (step 2 of
// context/current-feature.md) — one trigger path, not two racing ones.
// All that's left here is the plain <a href> fallback's direction
// tagging: whenever carousel.js can't run at all (fetch/DOMParser/
// pushState unsupported, or JS disabled outright), these prev/next links
// still work as normal navigation with no JS involvement, and get the
// generic crossfade instead of the directional slide — carousel.js has
// its own copy of this same tagging for when *it* falls back mid-swap
// (fetch failure, missing markup), see its pendingFallbackDirection.

const nav = document.querySelector('.cz-artwork-nav');

if (nav) {
	const prevBtn = nav.querySelector('.cz-artwork-nav__prev');
	const nextBtn = nav.querySelector('.cz-artwork-nav__next');

	// Which way we're going, read by the `pageswap` listener below right
	// as the browser starts navigating away, so it can tag the transition
	// with a direction (see global.css's `:active-view-transition-type`
	// rules for the actual slide animation). Matches
	// view-transition-name'd elements — see .single-artwork
	// .wp-block-post-featured-image img in global.css — into a directional
	// slide instead of the default stationary crossfade.
	let pendingDirection = null;

	// `pageswap` is the correct hook for this: it fires on the document
	// you're navigating *away* from, right as a cross-document view
	// transition is about to start, and the type it adds here carries
	// through to the new document's half of the same transition. Does
	// nothing if the browser doesn't support view transitions at all
	// (event.viewTransition is undefined then) — same graceful
	// degradation as everywhere else in this feature.
	window.addEventListener('pageswap', (event) => {
		if (event.viewTransition && pendingDirection) {
			event.viewTransition.types.add(pendingDirection);
		}
	});

	prevBtn?.addEventListener('click', () => {
		pendingDirection = 'cz-nav-prev';
	});
	nextBtn?.addEventListener('click', () => {
		pendingDirection = 'cz-nav-next';
	});
}
