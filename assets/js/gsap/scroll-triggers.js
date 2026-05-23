/**
 * GSAP ScrollTrigger Animations
 *
 * Section-level scroll-triggered reveals.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  gsap.registerPlugin(ScrollTrigger);

  window.annaScrollTriggersInit = function () {

    // ── Fade-up reveals ──────────────────────────────────────────────────
    gsap.utils.toArray('.anna-reveal').forEach(function (el) {
      gsap.from(el, {
        y:       40,
        opacity: 0,
        duration: 0.8,
        ease:    'power2.out',
        scrollTrigger: {
          trigger: el,
          start:   'top 85%',
          once:    true,
        },
      });
    });

    // ── Left reveals ─────────────────────────────────────────────────────
    gsap.utils.toArray('.anna-reveal--left').forEach(function (el) {
      gsap.from(el, {
        x:       -40,
        opacity: 0,
        duration: 0.8,
        ease:    'power2.out',
        scrollTrigger: {
          trigger: el,
          start:   'top 85%',
          once:    true,
        },
      });
    });

    // ── Right reveals ────────────────────────────────────────────────────
    gsap.utils.toArray('.anna-reveal--right').forEach(function (el) {
      gsap.from(el, {
        x:       40,
        opacity: 0,
        duration: 0.8,
        ease:    'power2.out',
        scrollTrigger: {
          trigger: el,
          start:   'top 85%',
          once:    true,
        },
      });
    });

    // ── Scale reveals ────────────────────────────────────────────────────
    gsap.utils.toArray('.anna-reveal--scale').forEach(function (el) {
      gsap.from(el, {
        scale:   0.92,
        opacity: 0,
        duration: 0.7,
        ease:    'power2.out',
        scrollTrigger: {
          trigger: el,
          start:   'top 85%',
          once:    true,
        },
      });
    });

    // ── Stagger children ─────────────────────────────────────────────────
    gsap.utils.toArray('.anna-stagger').forEach(function (container) {
      var children = container.children;
      if (!children.length) return;

      gsap.from(children, {
        y:       30,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        ease:    'power2.out',
        scrollTrigger: {
          trigger: container,
          start:   'top 85%',
          once:    true,
        },
      });
    });

    // ── Service cards hover motion ───────────────────────────────────────
    document.querySelectorAll('.anna-card--service').forEach(function (card) {
      card.addEventListener('mouseenter', function () {
        gsap.to(card, { y: -8, duration: 0.3, ease: 'power2.out' });
      });
      card.addEventListener('mouseleave', function () {
        gsap.to(card, { y: 0, duration: 0.4, ease: 'power2.inOut' });
      });
    });

    // ── Recognition items stagger ────────────────────────────────────────
    var recItems = document.querySelectorAll('.anna-recognition__item');
    if (recItems.length) {
      gsap.from(recItems, {
        x:       -20,
        opacity: 0,
        duration: 0.5,
        stagger: 0.08,
        ease:    'power2.out',
        scrollTrigger: {
          trigger: '.anna-recognition__list',
          start:   'top 80%',
          once:    true,
        },
      });
    }

    // ── Eyebrow line animation ───────────────────────────────────────────
    gsap.utils.toArray('.anna-eyebrow::before').forEach(function (line) {
      gsap.from(line, {
        scaleX:  0,
        duration: 0.6,
        ease:    'power2.out',
        scrollTrigger: {
          trigger: line.parentElement,
          start:   'top 85%',
          once:    true,
        },
      });
    });
  };
})();
