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
  var lastScrollY = window.scrollY || window.pageYOffset;
  var transparentHeader = header && header.classList.contains('anna-header--transparent');
  var threshold = transparentHeader ? 420 : 12;

  if (!header) return;

  function onScroll() {
    var scrollY = window.scrollY || window.pageYOffset;
    var scrollDown = scrollY > lastScrollY;
    var delta = Math.abs(scrollY - lastScrollY);

    if (scrollY > threshold && !scrolled) {
      header.classList.add('is-scrolled');
      scrolled = true;
    } else if (scrollY <= threshold && scrolled) {
      header.classList.remove('is-scrolled');
      header.classList.remove('is-hiding');
      scrolled = false;
    }

    if (scrolled && delta > 8) {
      if (scrollDown && scrollY > threshold + 80) {
        header.classList.add('is-hiding');
      } else {
        header.classList.remove('is-hiding');
      }
    } else if (!scrolled) {
      header.classList.remove('is-hiding');
    }

    lastScrollY = scrollY;
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

  // Check on load.
  onScroll();
})();
