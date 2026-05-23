/**
 * Stats Counter JS
 *
 * Animates stat numbers when they scroll into view.
 * Uses IntersectionObserver for lazy init.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  var stats = document.querySelectorAll('[data-stat-value]');
  if (!stats.length) return;

  function parseStatValue(str) {
    var numeric = parseFloat(str.replace(/[^0-9.]/g, ''));
    var suffix  = str.replace(/[0-9.]/g, '').trim();
    return { number: numeric || 0, suffix: suffix };
  }

  function animateCounter(el) {
    var raw    = el.getAttribute('data-stat-value');
    var parsed = parseStatValue(raw);
    var target = parsed.number;
    var suffix = parsed.suffix;
    var valueEl = el.querySelector('.anna-hero__stat-value');

    if (!valueEl || el.dataset.animated === 'true') return;
    el.dataset.animated = 'true';

    if (typeof gsap !== 'undefined') {
      var obj = { val: 0 };
      gsap.to(obj, {
        val:      target,
        duration: 2,
        ease:     'power2.out',
        onUpdate: function () {
          var display = target % 1 === 0
            ? Math.round(obj.val)
            : obj.val.toFixed(1);
          valueEl.textContent = display + suffix;
        },
      });
    } else {
      valueEl.textContent = raw;
    }
  }

  // Use IntersectionObserver for lazy init.
  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    stats.forEach(function (stat) { observer.observe(stat); });
  } else {
    stats.forEach(animateCounter);
  }
})();
