const panel    = document.getElementById('cz-sticky-nav-panel');
const openBtn  = document.querySelector('.cz-sticky-nav__open');
const closeBtn = panel?.querySelector('.cz-sticky-nav__close');
const linksEl  = panel?.querySelector('.cz-sticky-nav__links');

const desktopNav = document.querySelector('.cz-sticky-nav__inner .wp-block-navigation__container');
if (desktopNav && linksEl) {
    linksEl.innerHTML = desktopNav.innerHTML;
    linksEl.querySelectorAll('li, .wp-block-navigation-item').forEach((item, i) => {
        item.style.transitionDelay = `${200 + i * 80}ms`;
    });
}

const open = () => {
    panel.classList.remove('is-closing');
    panel.classList.add('is-open');
    openBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
};

const close = () => {
    panel.classList.replace('is-open', 'is-closing');
    openBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    panel.querySelector('.cz-sticky-nav__drawer').addEventListener(
        'animationend',
        () => panel.classList.remove('is-closing'),
        { once: true }
    );
};

openBtn?.addEventListener('click', open);
closeBtn?.addEventListener('click', close);
panel?.addEventListener('click', e => { if (e.target === panel) close(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
