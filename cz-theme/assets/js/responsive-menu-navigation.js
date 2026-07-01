/**
 * Custom slide-in/out animation for the WP Navigation overlay.
 *
 * Uses capture-phase listeners on WP's own open/close buttons so our code
 * fires BEFORE WordPress's handler. We manage `is-menu-open` ourselves;
 * the MutationObserver catches any state WP sneaks through anyway.
 */
(function () {
  function init() {
    const nav     = document.querySelector('.footer-nav .wp-block-navigation');
    if (!nav) return;

    const overlay  = nav.querySelector('.wp-block-navigation__responsive-container');
    const openBtn  = nav.querySelector('.wp-block-navigation__responsive-container-open');
    const closeBtn = nav.querySelector('.wp-block-navigation__responsive-container-close');

    if (!overlay) return;

    // Ensure overlay starts hidden without any inline display from WP
    overlay.style.display = 'none';
    overlay.classList.remove('is-menu-open');

    function openMenu() {
      overlay.style.display = 'flex'; // must be flex before transition starts
      void overlay.offsetHeight;      // force reflow so transition fires
      overlay.classList.add('is-menu-open');
    }

    function closeMenu() {
      overlay.classList.remove('is-menu-open');

      function onEnd(e) {
        if (e.propertyName === 'transform') {
          overlay.style.display = 'none';
          overlay.removeEventListener('transitionend', onEnd);
        }
      }
      overlay.addEventListener('transitionend', onEnd);

      // Fallback: hide after transition duration even if transitionend doesn't fire
      setTimeout(() => {
        if (!overlay.classList.contains('is-menu-open')) {
          overlay.style.display = 'none';
        }
      }, 600);
    }

    // Capture phase: intercept clicks before WP's own listeners
    if (openBtn) {
      openBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        openMenu();
      }, true);
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closeMenu();
      }, true);
    }

    // Safety net: if WP manages to set display:none while open, animate out instead
    const obs = new MutationObserver(function (mutations) {
      for (const m of mutations) {
        if (m.type === 'attributes' && m.attributeName === 'style') {
          if (overlay.style.display === 'none' && overlay.classList.contains('is-menu-open')) {
            overlay.style.display = 'flex';
            void overlay.offsetHeight;
            closeMenu();
          }
        }
      }
    });
    obs.observe(overlay, { attributes: true, attributeFilter: ['style'] });

    // Close on backdrop click (area outside the dialog content)
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeMenu();
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay.classList.contains('is-menu-open')) {
        closeMenu();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
