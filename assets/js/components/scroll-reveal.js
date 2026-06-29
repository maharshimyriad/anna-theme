/**
 * Scroll Reveal JS
 *
 * Lightweight IntersectionObserver-based reveal for elements
 * that don't need full GSAP (CSS fallback for .anna-reveal).
 *
 * Also reruns after AJAX page transitions via the `pageLoaded` event.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  function initReveal() {
    // Auto-apply scroll reveal to page template sections (coaching, MHS, scaffolded pages, etc.).
    document.querySelectorAll('.anna-main section').forEach(function (el) {
      if (!/\banna-reveal/.test(el.className)) {
        el.classList.add('anna-reveal');
      }
    });

    var reveals = document.querySelectorAll('.anna-reveal, .anna-reveal--left, .anna-reveal--right, .anna-reveal--scale, .anna-stagger');

    if (!reveals.length) return;

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, {
        rootMargin: '0px 0px -20px 0px',
        threshold:  0,
      });

      reveals.forEach(function (el) { observer.observe(el); });
    } else {
      // Fallback: show everything.
      reveals.forEach(function (el) { el.classList.add('is-visible'); });
    }
  }

  initReveal();

  // Reinitialize after AJAX page transitions.
  // ThemeInitializer in transition-manager.js also calls initReveal() internally,
  // but listening here keeps this module independently correct.
  document.addEventListener('pageLoaded', initReveal);
})();
