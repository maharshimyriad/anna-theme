/**
 * Header Component JS
 *
 * Sticky header scroll behavior: transparent → solid transition.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  var header = document.getElementById('site-header');
  var scrolled = false;
  var ticking = false;
  var transparentHeader = header && header.classList.contains('anna-header--transparent');
  var threshold = transparentHeader ? 420 : 12;

  if (!header) return;

  function onScroll() {
    var scrollY = window.scrollY || window.pageYOffset;

    if (scrollY > threshold && !scrolled) {
      header.classList.add('is-scrolled');
      scrolled = true;
    } else if (scrollY <= threshold && scrolled) {
      header.classList.remove('is-scrolled');
      scrolled = false;
    }

    // Ensure header is always visible
    header.classList.remove('is-hiding');
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      window.requestAnimationFrame(function () {
        onScroll();
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });

  // Run on load
  onScroll();
})();