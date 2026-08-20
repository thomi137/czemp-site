// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

const nav = document.querySelector('.cz-artwork-nav');

if (nav) {
	const prevBtn = nav.querySelector('.cz-artwork-nav__prev');
	const nextBtn = nav.querySelector('.cz-artwork-nav__next');
	const prevUrl = prevBtn?.href;
	const nextUrl = nextBtn?.href;

	// Same fade-out overlay the mobile nav drawer uses before it navigates
	// away (blocks/sticky-nav/render.php + style.css) — reused here rather
	// than building a second one.
	const spinner = document.querySelector('.cz-spinner-overlay');

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

	const navigateTo = (url, direction) => {
		pendingDirection = direction;
		spinner?.classList.add('is-visible');
		window.location.href = url;
	};

	// Buttons stay plain <a href> links, not intercepted with
	// preventDefault — they keep working with zero JS (see the feature's
	// original design). This just records which one was clicked, ahead
	// of the browser's own normal navigation, so they get the same
	// directional slide as keyboard/swipe below instead of the plain
	// crossfade.
	prevBtn?.addEventListener('click', () => {
		pendingDirection = 'cz-nav-prev';
	});
	nextBtn?.addEventListener('click', () => {
		pendingDirection = 'cz-nav-next';
	});

	// Circular navigation needs no logic here — the prev/next hrefs
	// themselves already wrap (see render.php's modulo arithmetic). This
	// only adds two more ways to trigger the same navigation the buttons
	// already offer.
	document.addEventListener('keydown', (event) => {
		if (event.target.matches('input, textarea, [contenteditable="true"]')) {
			return;
		}
		if (event.key === 'ArrowLeft' && prevUrl) {
			navigateTo(prevUrl, 'cz-nav-prev');
		} else if (event.key === 'ArrowRight' && nextUrl) {
			navigateTo(nextUrl, 'cz-nav-next');
		}
	});

	const image = document.querySelector('.wp-block-post-featured-image');
	if (image) {
		let touchStartX = 0;
		let touchStartY = 0;

		image.addEventListener(
			'touchstart',
			(event) => {
				touchStartX = event.changedTouches[0].clientX;
				touchStartY = event.changedTouches[0].clientY;
			},
			{ passive: true }
		);

		image.addEventListener(
			'touchend',
			(event) => {
				const deltaX = event.changedTouches[0].clientX - touchStartX;
				const deltaY = event.changedTouches[0].clientY - touchStartY;

				// Horizontal-dominant threshold so a vertical scroll
				// gesture on the image is never mistaken for a swipe.
				if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > Math.abs(deltaY)) {
					if (deltaX < 0 && nextUrl) {
						navigateTo(nextUrl, 'cz-nav-next');
					} else if (deltaX > 0 && prevUrl) {
						navigateTo(prevUrl, 'cz-nav-prev');
					}
				}
			},
			{ passive: true }
		);
	}
}
