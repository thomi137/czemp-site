/**
 * WP 7.0 uses the Interactivity API (data-wp-on--click directives) to manage
 * the navigation overlay — traditional event-listener interception doesn't
 * work. We let WP own the is-menu-open class and state; CSS handles the
 * slide animation via visibility + transform transitions.
 *
 * This script only adds conveniences WP doesn't provide out of the box.
 */
(function () {
  function init() {
    const overlay = document.querySelector(
      '.footer-nav .wp-block-navigation__responsive-container'
    );
    if (!overlay) return;

    // Escape key: trigger WP's own close button so Interactivity API state stays in sync
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay.classList.contains('is-menu-open')) {
        const closeBtn = overlay.querySelector('.wp-block-navigation-overlay-close');
        if (closeBtn) closeBtn.click();
      }
    });

    // Backdrop click (tap outside the inner dialog panel)
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) {
        const closeBtn = overlay.querySelector('.wp-block-navigation-overlay-close');
        if (closeBtn) closeBtn.click();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
