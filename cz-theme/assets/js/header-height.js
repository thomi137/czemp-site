// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

function cz_setHeaderOffset() {
    const header = document.querySelector('.cz-header');
    if (!header) return;
    // Only the header's own height — NOT .bottom. .bottom would also bake
    // in whatever `top` offset the admin-bar CSS currently has applied
    // (see .admin-bar .cz-header in global.css), and that offset is
    // *also* added explicitly in CSS via --wp-admin--admin-bar--height
    // wherever this variable is consumed. Measuring .bottom here would
    // double-count that offset instead of cancelling it.
    const height = Math.ceil(header.getBoundingClientRect().height);
    document.documentElement.style.setProperty('--cz-header-height', height + 'px');
}

cz_setHeaderOffset();
window.addEventListener('load', cz_setHeaderOffset);
window.addEventListener('resize', cz_setHeaderOffset);
