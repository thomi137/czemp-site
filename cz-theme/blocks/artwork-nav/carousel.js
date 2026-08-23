// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

/* Standalone ES5 (var, function expressions only) — deliberate exception
   to the rest of the theme's modern-syntax view.js scripts, per direct
   request. fetch/DOMParser/pushState are Web APIs, not syntax, and stay
   in use.

   All 7 build steps of context/current-feature.md: fetch + container-
   patch, triggered by click, keyboard, and swipe (one path, not three —
   see artwork-nav/view.js, which used to own keyboard/swipe/click-
   tagging and now only keeps the no-JS/fallback pageswap tagging), URL
   kept in sync via pushState/popstate, stale in-flight requests
   aborted/ignored, next/prev neighbor images preloaded after each swap,
   directional slide animation on the featured image (prefers-reduced-
   motion respected), SEO/social meta + document.title + focus kept in
   sync with the artwork actually on screen. */

(function () {
	'use strict';

	if (!window.fetch || !window.DOMParser || !window.history || !window.history.pushState) {
		return; // no swap support — every <a href> keeps working as a plain reload
	}

	var nav = document.querySelector('.cz-artwork-nav');
	if (!nav) {
		return; // this artwork has no prev/next (collection has <2 members)
	}

	// Selectors for the containers this step patches. Listed once so step
	// 2+ can reuse them without redefining.
	var IMG_SELECTOR = '.wp-block-post-featured-image img';
	var BREADCRUMBS_SELECTOR = '.cz-breadcrumbs';
	var COLUMN_SELECTOR = '.wp-block-column.is-vertically-aligned-top';

	// inc/seo.php's full per-page tag set (description, Open Graph,
	// Twitter Card) — kept in sync from the fetched document's <head> on
	// every swap so a screen reader announcing the title, or a share
	// action from the browser chrome mid-session, reflects the artwork
	// actually on screen.
	var META_SELECTORS = [
		'meta[name="description"]',
		'meta[property="og:locale"]',
		'meta[property="og:type"]',
		'meta[property="og:site_name"]',
		'meta[property="og:title"]',
		'meta[property="og:url"]',
		'meta[property="og:description"]',
		'meta[property="og:image"]',
		'meta[property="og:image:width"]',
		'meta[property="og:image:height"]',
		'meta[property="og:image:alt"]',
		'meta[name="twitter:card"]',
		'meta[name="twitter:title"]',
		'meta[name="twitter:description"]',
		'meta[name="twitter:image"]',
	];

	// Direction tagging for the rare case swapToArtwork gives up and falls
	// back to a real navigation (fetch failed, required markup missing).
	// Kept local to this script rather than reaching into view.js's own
	// pendingDirection — that one still exists unchanged, for the
	// feature-detect-fail/no-JS case where this script never runs at all.
	// Both listen on the same `pageswap` event; only one is ever non-null
	// for a given real navigation.
	// Bumped on every swapToArtwork call; a fetch response only gets
	// applied if its id still matches the latest by the time it lands.
	// Loses a race gracefully if AbortController isn't there to cancel
	// the stale request outright — belt and suspenders, not either/or.
	var currentRequestId = 0;

	// The in-flight request's controller, so a newer swap can cancel a
	// still-pending older one outright rather than just letting its
	// response get discarded once it lands.
	var currentController = null;

	// Direction used to arrive at the artwork currently on screen, or null
	// for the original server-rendered load. NOT read from popstate's
	// event.state directly for the inversion below — event.state on a
	// popstate describes the entry being LANDED on, not the one being
	// LEFT, so inverting it would invert the wrong thing. This tracks the
	// outgoing entry's direction ourselves instead.
	var currentArrivalDirection = null;

	var pendingFallbackDirection = null;
	window.addEventListener('pageswap', function (event) {
		if (event.viewTransition && pendingFallbackDirection) {
			event.viewTransition.types.add(pendingFallbackDirection);
		}
	});

	function fallbackNavigate(url, direction) {
		if (direction) {
			pendingFallbackDirection = direction === 'next' ? 'cz-nav-next' : 'cz-nav-prev';
		}
		window.location.href = url;
	}

	function prefersReducedMotion() {
		return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
	}

	/**
	 * Animates the featured image from its current state to `newImg`'s,
	 * or patches it instantly when there's nothing to animate (no
	 * direction — e.g. the very first load's popstate baseline — or
	 * prefers-reduced-motion). A clone of the *old* image, pinned over
	 * the real one with getBoundingClientRect (position:fixed, so it
	 * doesn't need a positioned ancestor), slides out while the real
	 * <img> — patched to the new attributes right away — slides in from
	 * the other side. Cleans itself up on `animationend`, with a timeout
	 * fallback in case that never fires.
	 */
	function animateImagePatch(currentImg, newImg, direction) {
		if (!direction || prefersReducedMotion()) {
			patchImage(currentImg, newImg);
			return;
		}

		var outClass = direction === 'next' ? 'cz-carousel-slide-out-left' : 'cz-carousel-slide-out-right';
		var inClass = direction === 'next' ? 'cz-carousel-slide-in-right' : 'cz-carousel-slide-in-left';

		var rect = currentImg.getBoundingClientRect();
		var clone = currentImg.cloneNode(true);
		clone.removeAttribute('id');
		clone.style.position = 'fixed';
		clone.style.top = rect.top + 'px';
		clone.style.left = rect.left + 'px';
		clone.style.width = rect.width + 'px';
		clone.style.height = rect.height + 'px';
		clone.style.margin = '0';
		// Below every fixed piece of site chrome (header:20, sticky-nav:30)
		// — the clone is a slide of page content, not an overlay, so it
		// must stay under the header/nav it may pass behind while sliding,
		// not above them.
		clone.style.zIndex = '1';
		clone.style.pointerEvents = 'none';
		clone.className = clone.className + ' ' + outClass;
		document.body.appendChild(clone);

		patchImage(currentImg, newImg);
		currentImg.classList.add(inClass);

		var cleanedUp = false;
		function cleanup() {
			if (cleanedUp) {
				return;
			}
			cleanedUp = true;
			if (clone.parentNode) {
				clone.parentNode.removeChild(clone);
			}
			currentImg.classList.remove(inClass);
		}
		clone.addEventListener('animationend', cleanup);
		setTimeout(cleanup, 600);
	}

	// Copies the attributes a patched <img> actually needs — not a node
	// replacement; animateImagePatch above is what gives old/new a brief
	// visual overlap for the slide, not this function itself.
	function patchImage(currentImg, newImg) {
		currentImg.setAttribute('src', newImg.getAttribute('src') || '');
		var srcset = newImg.getAttribute('srcset');
		if (srcset) {
			currentImg.setAttribute('srcset', srcset);
		} else {
			currentImg.removeAttribute('srcset');
		}
		var sizes = newImg.getAttribute('sizes');
		if (sizes) {
			currentImg.setAttribute('sizes', sizes);
		} else {
			currentImg.removeAttribute('sizes');
		}
		currentImg.setAttribute('alt', newImg.getAttribute('alt') || '');
	}

	/**
	 * Fetches `url`, patches the current document in place. Falls back to
	 * a normal navigation on fetch failure or missing load-bearing markup.
	 *
	 * @param {string}  url            Target artwork permalink.
	 * @param {?string} direction      'next' or 'prev', or null for a
	 *                                 popstate-triggered swap (locked
	 *                                 contract: no stored direction means
	 *                                 no animation — step 6's concern,
	 *                                 nothing to animate yet in step 3).
	 * @param {boolean} [isPopstate]   True when called from the popstate
	 *                                 handler — the browser already moved
	 *                                 the history position, so this skips
	 *                                 pushState instead of pushing again.
	 */
	function swapToArtwork(url, direction, isPopstate) {
		if (currentController) {
			currentController.abort();
		}
		currentController = window.AbortController ? new AbortController() : null;
		var requestId = ++currentRequestId;

		fetch(url, currentController ? { signal: currentController.signal } : undefined)
			.then(function (response) {
				if (!response.ok) {
					throw new Error('bad response');
				}
				return response.text();
			})
			.then(function (html) {
				// A newer swap started (and, if AbortController exists,
				// already cancelled this fetch) while this one was still
				// on the wire — its response is stale, discard it.
				if (requestId !== currentRequestId) {
					return;
				}

				var newDoc = new DOMParser().parseFromString(html, 'text/html');

				var newImg = newDoc.querySelector(IMG_SELECTOR);
				var newColumn = newDoc.querySelector(COLUMN_SELECTOR);
				if (!newImg || !newColumn) {
					// Load-bearing containers missing — don't render a half page.
					fallbackNavigate(url, direction);
					return;
				}

				var currentImg = document.querySelector(IMG_SELECTOR);
				if (currentImg) {
					animateImagePatch(currentImg, newImg, direction);
				}

				var newBreadcrumbs = newDoc.querySelector(BREADCRUMBS_SELECTOR);
				var currentBreadcrumbs = document.querySelector(BREADCRUMBS_SELECTOR);
				if (currentBreadcrumbs) {
					currentBreadcrumbs.innerHTML = newBreadcrumbs ? newBreadcrumbs.innerHTML : '';
				}

				var currentColumn = document.querySelector(COLUMN_SELECTOR);
				if (currentColumn) {
					currentColumn.innerHTML = newColumn.innerHTML;
				}

				// Valid state: the new artwork's collection may have <2
				// members, so its own page never rendered a .cz-artwork-nav
				// at all — empty ours out to match rather than treating it
				// as a missing/broken container.
				var newNav = newDoc.querySelector('.cz-artwork-nav');
				nav.innerHTML = newNav ? newNav.innerHTML : '';

				if (!isPopstate) {
					history.pushState({ direction: direction }, '', url);
					currentArrivalDirection = direction;
				}

				syncHeadMeta(newDoc);
				focusNewHeading();
				preloadNeighborImages();
			})
			.catch(function (error) {
				// An intentional cancellation of a now-stale request, not a
				// real failure — the newer swap that superseded it owns the
				// outcome, this one does nothing.
				if (error && error.name === 'AbortError') {
					return;
				}
				if (requestId !== currentRequestId) {
					return;
				}
				fallbackNavigate(url, direction);
			});
	}

	// The browser has already moved the history position and updated
	// location.href by the time this fires — just patch the DOM to match.
	// Animation direction is the *inverse* of currentArrivalDirection (how
	// we got to the entry we're leaving, per the locked contract: undoing
	// a 'next' plays as 'prev'), not of event.state (which describes the
	// entry we're landing on). No stored arrival direction (the original
	// server-rendered load) means no inversion to make — instant swap,
	// same as the locked contract's baseline case.
	//
	// Accepted simplification: this correctly mirrors the tested case
	// (back after a forward move) but can't distinguish a *forward*
	// popstate (redo, via the forward button after having gone back) from
	// a backward one — plain browser history gives no such signal without
	// tracking a sequence number, which nothing here needed otherwise. A
	// forward-redo after a back may play no animation instead of
	// re-playing the original direction. Same "first pass" spirit as the
	// existing View Transition slide (see _single-artwork-fit.scss).
	window.addEventListener('popstate', function (event) {
		var animDirection = null;
		if (currentArrivalDirection === 'next') {
			animDirection = 'prev';
		} else if (currentArrivalDirection === 'prev') {
			animDirection = 'next';
		}

		currentArrivalDirection = event.state && event.state.direction ? event.state.direction : null;

		swapToArtwork(window.location.href, animDirection, true);
	});

	// Best-effort cache warm for one artwork's image — never touches the
	// DOM, never surfaces an error; a failed preload just means no warm
	// cache next click, not a user-facing problem.
	function preloadNeighborImage(url) {
		if (!url) {
			return;
		}
		fetch(url)
			.then(function (response) {
				if (!response.ok) {
					throw new Error('bad response');
				}
				return response.text();
			})
			.then(function (html) {
				var doc = new DOMParser().parseFromString(html, 'text/html');
				var img = doc.querySelector(IMG_SELECTOR);
				if (!img) {
					return;
				}
				var preload = new Image();
				var srcset = img.getAttribute('srcset');
				var sizes = img.getAttribute('sizes');
				if (srcset) {
					preload.srcset = srcset;
				}
				if (sizes) {
					preload.sizes = sizes;
				}
				preload.src = img.getAttribute('src') || '';
			})
			.catch(function () {});
	}

	// Immediate next/prev neighbors only — no chaining into their own
	// neighbors, per the feature's explicit "no infinite-scroll-style
	// prefetching" scope cut.
	function preloadNeighborImages() {
		preloadNeighborImage(getTriggerUrl('next'));
		preloadNeighborImage(getTriggerUrl('prev'));
	}

	// Creates/updates/removes each tag in META_SELECTORS to match newDoc,
	// by whichever of name="…"/property="…" it's keyed on — handles a tag
	// that's present on one artwork's page but not another's (e.g. no
	// og:image at all when an artwork has no featured image) in either
	// direction, not just a content-attribute overwrite.
	function syncHeadMeta(newDoc) {
		document.title = newDoc.title;

		for (var i = 0; i < META_SELECTORS.length; i++) {
			var selector = META_SELECTORS[i];
			var newTag = newDoc.querySelector(selector);
			var currentTag = document.querySelector(selector);

			if (!newTag) {
				if (currentTag && currentTag.parentNode) {
					currentTag.parentNode.removeChild(currentTag);
				}
				continue;
			}

			if (!currentTag) {
				currentTag = document.createElement('meta');
				if (newTag.hasAttribute('name')) {
					currentTag.setAttribute('name', newTag.getAttribute('name'));
				}
				if (newTag.hasAttribute('property')) {
					currentTag.setAttribute('property', newTag.getAttribute('property'));
				}
				document.head.appendChild(currentTag);
			}
			currentTag.setAttribute('content', newTag.getAttribute('content') || '');
		}
	}

	// tabindex="-1" + focus so a screen reader announces the new artwork's
	// title right after a swap instead of leaving focus stranded on
	// whatever triggered it (a nav button, or nothing at all after a
	// keyboard/swipe trigger) — removed again on blur so the heading
	// doesn't permanently join the tab order.
	function focusNewHeading() {
		var heading = document.querySelector(COLUMN_SELECTOR + ' h1');
		if (!heading) {
			return;
		}
		heading.setAttribute('tabindex', '-1');
		heading.focus({ preventScroll: true });
		heading.addEventListener('blur', function onBlur() {
			heading.removeAttribute('tabindex');
			heading.removeEventListener('blur', onBlur);
		});
	}

	// Live lookups, not cached at load — nav's own innerHTML gets replaced
	// on every swap, so yesterday's prevBtn/nextBtn nodes are stale after
	// the first navigation (unlike view.js's old version, which never
	// needed to re-read them because every trigger was a full reload).
	function getTriggerUrl(direction) {
		var link = nav.querySelector(direction === 'next' ? '.cz-artwork-nav__next' : '.cz-artwork-nav__prev');
		return link ? link.href : null;
	}

	nav.addEventListener('click', function (event) {
		var link = event.target.closest('.cz-artwork-nav__prev, .cz-artwork-nav__next');
		if (!link) {
			return;
		}
		event.preventDefault();
		var direction = link.classList.contains('cz-artwork-nav__next') ? 'next' : 'prev';
		swapToArtwork(link.href, direction);
	});

	// Circular navigation needs no logic here — the prev/next hrefs
	// already wrap (render.php's modulo arithmetic carries into every
	// fetched document the same way).
	document.addEventListener('keydown', function (event) {
		if (event.target.matches('input, textarea, [contenteditable="true"]')) {
			return;
		}
		if (event.key === 'ArrowLeft') {
			var prevUrl = getTriggerUrl('prev');
			if (prevUrl) {
				swapToArtwork(prevUrl, 'prev');
			}
		} else if (event.key === 'ArrowRight') {
			var nextUrl = getTriggerUrl('next');
			if (nextUrl) {
				swapToArtwork(nextUrl, 'next');
			}
		}
	});

	var image = document.querySelector('.wp-block-post-featured-image');
	if (image) {
		var touchStartX = 0;
		var touchStartY = 0;

		image.addEventListener(
			'touchstart',
			function (event) {
				touchStartX = event.changedTouches[0].clientX;
				touchStartY = event.changedTouches[0].clientY;
			},
			{ passive: true }
		);

		image.addEventListener(
			'touchend',
			function (event) {
				var deltaX = event.changedTouches[0].clientX - touchStartX;
				var deltaY = event.changedTouches[0].clientY - touchStartY;

				// Horizontal-dominant threshold so a vertical scroll
				// gesture on the image is never mistaken for a swipe.
				if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > Math.abs(deltaY)) {
					if (deltaX < 0) {
						var nextUrl = getTriggerUrl('next');
						if (nextUrl) {
							swapToArtwork(nextUrl, 'next');
						}
					} else {
						var prevUrl = getTriggerUrl('prev');
						if (prevUrl) {
							swapToArtwork(prevUrl, 'prev');
						}
					}
				}
			},
			{ passive: true }
		);
	}
})();
