// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

function cz_setHeaderOffset() {
    const header = document.querySelector('.cz-header');
    if (!header) return;
    const offset = Math.ceil(header.getBoundingClientRect().bottom);
    document.documentElement.style.setProperty('--cz-header-height', offset + 'px');
}

cz_setHeaderOffset();
window.addEventListener('load', cz_setHeaderOffset);
window.addEventListener('resize', cz_setHeaderOffset);
