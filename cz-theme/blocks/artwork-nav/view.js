// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

const nav = document.querySelector('.cz-artwork-nav');

if (nav) {
	const prevUrl = nav.querySelector('.cz-artwork-nav__prev')?.href;
	const nextUrl = nav.querySelector('.cz-artwork-nav__next')?.href;

	// Circular navigation needs no logic here — the prev/next hrefs
	// themselves already wrap (see render.php's modulo arithmetic). This
	// only adds two more ways to trigger the same navigation the buttons
	// already offer.
	document.addEventListener('keydown', (event) => {
		if (event.target.matches('input, textarea, [contenteditable="true"]')) {
			return;
		}
		if (event.key === 'ArrowLeft' && prevUrl) {
			window.location.href = prevUrl;
		} else if (event.key === 'ArrowRight' && nextUrl) {
			window.location.href = nextUrl;
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
						window.location.href = nextUrl;
					} else if (deltaX > 0 && prevUrl) {
						window.location.href = prevUrl;
					}
				}
			},
			{ passive: true }
		);
	}
}
