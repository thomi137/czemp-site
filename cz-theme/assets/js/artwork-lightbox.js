// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Click-to-enlarge lightbox for the single-artwork featured image (and, if
// present, the secondary/companion image stacked below it) — full image,
// no cropping, dedicated focused view. Compromise for the inline page
// staying width-first/uncapped (see _single-artwork-fit.scss): anyone who
// wants to see a piece with zero layout compromise can click through.
//
// Reads the DOM live at open-time, not cached at page load, so it stays
// correct across blocks/artwork-nav/carousel.js's prev/next swaps (which
// patch the featured/secondary <img> elements in place) with no
// coordination needed between the two scripts.

function czPrefersReducedMotion() {
    return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
}

function czBuildLightboxImg(sourceImg, extraClass) {
    const img = document.createElement('img');
    img.className = extraClass ? 'cz-lightbox__img ' + extraClass : 'cz-lightbox__img';

    const srcset = sourceImg.getAttribute('srcset');
    if (srcset) {
        img.setAttribute('srcset', srcset);
        // The inline column's own `sizes` hint (inc/frontend.php) is tuned
        // for its much narrower on-page box — reusing it here would keep
        // the browser on a small srcset candidate even though the
        // lightbox renders far larger. 100vw lets it pick the largest
        // candidate available (WordPress includes the original up to its
        // full size by default).
        img.setAttribute('sizes', '100vw');
    }
    img.setAttribute('src', sourceImg.getAttribute('src') || '');
    img.alt = sourceImg.getAttribute('alt') || '';
    return img;
}

// Replicates what object-fit:contain renders `cloneImg` at — its actual
// picture width, not just its (fixed, 100%) box width. Needed because the
// featured slide is height-constrained (100dvh, see _artwork-lightbox.scss),
// so its rendered width varies with the photo's own aspect ratio; there's
// no plain-CSS way to hand that computed value to a sibling element.
//
// Takes the natural (intrinsic) dimensions from `sourceImg` — the featured
// image already visible on the page itself — rather than from `cloneImg`
// (the lightbox's own freshly created <img>, built by czBuildLightboxImg).
// `cloneImg`'s own naturalWidth/naturalHeight are only populated once ITS
// OWN fetch+decode finishes, which used to be awaited via a 'load'
// listener (08.09.2026: found responsible for secondary images silently
// falling back to the much-too-wide CSS max-width whenever that never
// fired cleanly — same-origin, so no reason it shouldn't, but not worth
// depending on a second decode of an image the page already has fully
// loaded). `cloneImg.getBoundingClientRect()` is still needed for the
// BOX (rect.width/height) — but that only reflects CSS width:100%/
// height:100% of a fixed-size parent, settled the instant the element is
// laid out, independent of whether its own image data has arrived yet.
function czContainWidth(cloneImg, sourceImg) {
    const rect = cloneImg.getBoundingClientRect();
    const naturalWidth = cloneImg.naturalWidth || sourceImg.naturalWidth;
    const naturalHeight = cloneImg.naturalHeight || sourceImg.naturalHeight;
    if (!naturalWidth || !naturalHeight || !rect.width || !rect.height) {
        return rect.width;
    }
    const boxRatio = rect.width / rect.height;
    const imgRatio = naturalWidth / naturalHeight;
    return imgRatio > boxRatio ? rect.width : rect.height * imgRatio;
}

// Caps the secondary image to at most the featured image's own rendered
// width (07.09.2026 ask: "secondary should always be at most the width of
// the orig"). Applies synchronously — see czContainWidth above for why
// this no longer waits on any 'load' event — and re-measured on resize/
// orientation change; returns a cleanup function that removes that
// listener, since it must not outlive the lightbox instance it belongs to.
function czMatchSecondaryWidth(featuredCloneImg, featuredSourceImg, secondaryImg) {
    function apply() {
        const width = czContainWidth(featuredCloneImg, featuredSourceImg);
        if (width) {
            secondaryImg.style.maxWidth = Math.round(width) + 'px';
        }
    }
    apply();
    window.addEventListener('resize', apply);
    return function cleanup() {
        window.removeEventListener('resize', apply);
    };
}

// Builds/replaces the lightbox's content (featured slide + optional
// secondary image + scroll hint) inside `scroll`. One shared code path for
// both the initial open and every carousel.js swap while already open (see
// the cz:artwork-swapped listener below), so the two can't drift apart.
// Returns a cleanup function for whatever it wired up (or null).
function czBuildLightboxContent(scroll, featuredSourceImg, secondarySourceImg) {
    scroll.innerHTML = '';

    const slide = document.createElement('div');
    slide.className = 'cz-lightbox__slide';
    const featuredImg = czBuildLightboxImg(featuredSourceImg, 'cz-lightbox__img--featured');
    slide.appendChild(featuredImg);
    scroll.appendChild(slide);

    if (!secondarySourceImg) {
        return null;
    }

    // Visual cue that there's more below — otherwise nothing on screen
    // hints that scrolling reveals a second image. Same "chevron-track"
    // motif as blocks/current-exhibitions and animated-button's
    // chevron-track option (site's existing "there's more" cue), reused
    // here rather than inventing a new icon. Toggles on scroll position
    // rather than a one-shot fade-out, so it comes back if the visitor
    // scrolls back up past the threshold (08.09.2026 ask) instead of
    // permanently disappearing after the first scroll.
    const hint = document.createElement('div');
    hint.className = 'cz-lightbox__scroll-hint';
    hint.setAttribute('aria-hidden', 'true');
    hint.innerHTML =
        '<svg class="cz-lightbox__scroll-hint-icon" viewBox="0 0 24 26" width="20" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
        '<polyline class="cz-lightbox__chevron cz-lightbox__chevron--top" points="6 5 12 10 18 5"></polyline>' +
        '<polyline class="cz-lightbox__chevron cz-lightbox__chevron--middle" points="6 12 12 17 18 12"></polyline>' +
        '<polyline class="cz-lightbox__chevron cz-lightbox__chevron--bottom" points="6 19 12 24 18 19"></polyline>' +
        '</svg>';
    slide.appendChild(hint);

    function onScroll() {
        hint.classList.toggle('cz-lightbox__scroll-hint--hidden', scroll.scrollTop > 20);
    }
    scroll.addEventListener('scroll', onScroll);

    const secondaryWrap = document.createElement('div');
    secondaryWrap.className = 'cz-lightbox__secondary-wrap';
    const secondaryImg = czBuildLightboxImg(secondarySourceImg, 'cz-lightbox__img--secondary');
    secondaryWrap.appendChild(secondaryImg);
    scroll.appendChild(secondaryWrap);

    const widthMatchCleanup = czMatchSecondaryWidth(featuredImg, featuredSourceImg, secondaryImg);

    // No longer self-removing (see above) — `scroll` itself survives every
    // rebuild (only its children get wiped), so without this the listener
    // would stack up fresh on every artwork swap instead of being replaced.
    return function cleanup() {
        scroll.removeEventListener('scroll', onScroll);
        if (widthMatchCleanup) {
            widthMatchCleanup();
        }
    };
}

// Tracks whatever czBuildLightboxContent's returned cleanup (scroll-hint
// listener + width-match resize listener) is currently active, across both
// the initial open and any later cz:artwork-swapped rebuild, so a swap (or
// a close) always cleans up the previous one instead of leaking listeners.
let czContentCleanup = null;

function czSetContentCleanup(cleanup) {
    if (czContentCleanup) {
        czContentCleanup();
    }
    czContentCleanup = cleanup;
}

// Keyboard-arrow navigation (blocks/artwork-nav/carousel.js's document-level
// keydown listener) isn't blocked by the lightbox overlay's stacking the way
// a click/swipe on the now-covered featured image is — so arrow keys still
// swap the underlying artwork while the lightbox is open. Rather than
// disabling that (or duplicating carousel.js's own next/prev triggers here),
// just mirror whatever it lands on: carousel.js dispatches this event once
// its patch of the background DOM is complete, and this rebuilds the
// lightbox's own content from that same now-current DOM. Net effect: arrow
// keys keep working while the lightbox is open, and the lightbox itself
// stays in sync instead of quietly going stale until closed.
document.addEventListener('cz:artwork-swapped', function () {
    const overlay = document.querySelector('.cz-lightbox');
    const scroll = overlay && overlay.querySelector('.cz-lightbox__scroll');
    const featuredImg = document.querySelector('.wp-block-post-featured-image img');
    if (!scroll || !featuredImg) {
        return;
    }

    const secondaryImg = document.querySelector('.cz-artwork-secondary-image img');

    function rebuild() {
        czSetContentCleanup(czBuildLightboxContent(scroll, featuredImg, secondaryImg));
        // Start the new artwork at its featured image, not wherever the
        // previous one happened to be scrolled to.
        scroll.scrollTop = 0;
    }

    if (czPrefersReducedMotion()) {
        rebuild();
        return;
    }

    // Crossfade rather than the previous instant swap — fade the old
    // content out (.cz-lightbox__scroll's own opacity transition, see
    // _artwork-lightbox.scss), rebuild once that's finished, then let the
    // class removal fade the new content back in.
    let rebuilt = false;
    function onTransitionEnd(event) {
        if (rebuilt || (event && event.propertyName !== 'opacity')) {
            return;
        }
        rebuilt = true;
        scroll.removeEventListener('transitionend', onTransitionEnd);
        rebuild();
        // Force a reflow so removing the class right after adding it
        // still transitions back in, instead of the browser coalescing
        // both into a no-op.
        void scroll.offsetWidth;
        scroll.classList.remove('cz-lightbox__scroll--transitioning');
    }
    scroll.addEventListener('transitionend', onTransitionEnd);
    setTimeout(onTransitionEnd, 500);
    scroll.classList.add('cz-lightbox__scroll--transitioning');
});

function czOpenLightbox(trigger) {
    const featuredImg = document.querySelector('.wp-block-post-featured-image img');
    if (!featuredImg) {
        return;
    }
    const secondaryImg = document.querySelector('.cz-artwork-secondary-image img');

    const overlay = document.createElement('div');
    overlay.className = 'cz-lightbox';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', 'Bild in voller Grösse');

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'cz-lightbox__close';
    closeButton.setAttribute('aria-label', 'Schliessen');
    closeButton.innerHTML = '&times;';

    const scroll = document.createElement('div');
    scroll.className = 'cz-lightbox__scroll';

    // `scroll` must already be attached to the document before content
    // goes in: czMatchSecondaryWidth (via czBuildLightboxContent) measures
    // the featured image's box synchronously now (08.09.2026), and
    // getBoundingClientRect() on a still-detached element returns an
    // all-zero rect — building content first (the old order) silently
    // fed that zero straight into the "at most" cap.
    overlay.appendChild(closeButton);
    overlay.appendChild(scroll);
    document.body.appendChild(overlay);
    document.body.classList.add('cz-lightbox-open');

    czSetContentCleanup(czBuildLightboxContent(scroll, featuredImg, secondaryImg));

    let isClosing = false;

    function close() {
        if (isClosing) {
            return;
        }
        isClosing = true;

        document.removeEventListener('keydown', onKeydown);
        overlay.removeEventListener('click', onOverlayClick);
        czSetContentCleanup(null);

        function finish() {
            overlay.remove();
            document.body.classList.remove('cz-lightbox-open');
            if (trigger) {
                trigger.focus();
            }
        }

        if (czPrefersReducedMotion()) {
            finish();
            return;
        }

        // Plays the reverse of the entrance animation (_artwork-lightbox.scss)
        // before actually removing the element. animationend can fire more
        // than once here (it bubbles from .cz-lightbox__scroll's own
        // closing animation too) — harmless, `remove()`/classList removal
        // on an already-detached node is a no-op, same guard pattern as
        // carousel.js's own animateImagePatch() cleanup.
        let cleanedUp = false;
        function onAnimationEnd() {
            if (cleanedUp) {
                return;
            }
            cleanedUp = true;
            finish();
        }
        overlay.addEventListener('animationend', onAnimationEnd);
        setTimeout(onAnimationEnd, 600);
        overlay.classList.add('cz-lightbox--closing');
    }

    function onKeydown(event) {
        if (event.key === 'Escape') {
            close();
        }
    }

    // Closes on a click anywhere that isn't directly on an image — covers
    // the backdrop itself and every wrapper around the images (slide,
    // secondary-wrap) alike, without needing to track each one by identity.
    function onOverlayClick(event) {
        if (event.target.tagName !== 'IMG') {
            close();
        }
    }

    closeButton.addEventListener('click', close);
    overlay.addEventListener('click', onOverlayClick);
    document.addEventListener('keydown', onKeydown);
    closeButton.focus();
}

function czInitArtworkLightbox() {
    const wrapper = document.querySelector('.wp-block-post-featured-image');
    const img = wrapper && wrapper.querySelector('img');
    if (!img) {
        return;
    }

    // Progressive enhancement: the button/tabindex semantics only get
    // added once this script actually runs, so a no-JS visitor just sees
    // a plain, non-interactive image rather than a "button" that does
    // nothing.
    img.setAttribute('tabindex', '0');
    img.setAttribute('role', 'button');
    img.setAttribute('aria-label', 'Bild vergrössern');
    img.classList.add('cz-lightbox-trigger');

    img.addEventListener('click', function () {
        czOpenLightbox(img);
    });
    img.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            czOpenLightbox(img);
        }
    });
}

czInitArtworkLightbox();
