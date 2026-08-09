// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

if (window.matchMedia('(hover: none)').matches) {
    // Reveal the overlay once a tile is substantially on screen. The
    // previous approach only revealed a tile once it scrolled through the
    // *center* 40% of the viewport (rootMargin: '-30% 0px -30% 0px') —
    // which the last tile(s) on the page can never do once there's no
    // more room to scroll, leaving their overlay permanently unreadable
    // on mobile. A plain visibility threshold has no such dead zone: it
    // fires for every tile, including whatever settles at the bottom of
    // the page once scrolling maxes out.
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            entry.target.classList.toggle('is-visible', entry.isIntersecting);
        });
    }, { threshold: 0.6 });

    document.querySelectorAll('.gallery-item').forEach(el => observer.observe(el));
}
