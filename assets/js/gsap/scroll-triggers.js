/**
 * GSAP ScrollTrigger Animations
 *
 * Premium scroll-triggered reveals for all pages.
 * Uses expo.out easing, subtle distances, and overlapping timelines
 * for a silky, high-end feel.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  if (window.matchMedia('(prefers-reduce-motion: reduce)').matches) return;

  gsap.registerPlugin(ScrollTrigger);

  // ── Shared defaults ───────────────────────────────────────────────────────
  var DURATION    = 1.05;
  var EASE        = 'expo.out';
  var DISTANCE    = 28;
  var TRIGGER_POS = 'top 85%';

  // Force GPU compositing on all animated elements up front.
  // GSAP clears it after animation completes to free memory.
  function prime(els) {
    gsap.set(els, { force3D: true });
  }

  // ── Single-element reveal factory ─────────────────────────────────────────
  function reveal(selector, fromVars, options) {
    var opts = options || {};
    gsap.utils.toArray(selector).forEach(function (el) {
      prime(el);
      gsap.fromTo(
        el,
        Object.assign({ autoAlpha: 0 }, fromVars),
        Object.assign(
          {
            autoAlpha:  1,
            x:          0,
            y:          0,
            scale:      1,
            duration:   opts.duration  || DURATION,
            ease:       opts.ease      || EASE,
            clearProps: 'willChange,force3D',
          },
          {
            scrollTrigger: {
              trigger: el,
              start:   opts.start || TRIGGER_POS,
              once:    true,
            },
          }
        )
      );
    });
  }

  // ── Stagger reveal ────────────────────────────────────────────────────────
  function stagger(container) {
    var children = Array.from(container.children);
    if (!children.length) return;

    prime(children);
    gsap.fromTo(
      children,
      { autoAlpha: 0, y: 20 },
      {
        autoAlpha:  1,
        y:          0,
        duration:   0.85,
        ease:       'expo.out',
        clearProps: 'willChange,force3D',
        stagger: {
          amount: Math.min(0.12 + children.length * 0.06, 0.8),
          ease:   'power2.inOut',
          from:   'start',
        },
        scrollTrigger: {
          trigger: container,
          start:   'top 85%',
          once:    true,
        },
      }
    );
  }

  // ── Card hover lift ───────────────────────────────────────────────────────
  function hoverLift(selector, yAmt) {
    document.querySelectorAll(selector).forEach(function (card) {
      card.addEventListener('mouseenter', function () {
        gsap.to(card, { y: -(yAmt || 6), duration: 0.35, ease: 'power2.out' });
      });
      card.addEventListener('mouseleave', function () {
        gsap.to(card, { y: 0, duration: 0.5, ease: 'expo.out' });
      });
    });
  }

  // ── Init ──────────────────────────────────────────────────────────────────
  window.annaScrollTriggersInit = function () {

    // Fade up
    reveal('.anna-reveal', { y: DISTANCE });

    // Slide in from left — slightly more travel for directional feel
    reveal('.anna-reveal--left',  { x: -(DISTANCE + 8) }, { ease: 'expo.out' });

    // Slide in from right
    reveal('.anna-reveal--right', { x:  (DISTANCE + 8) }, { ease: 'expo.out' });

    // Scale up — subtle, premium
    reveal('.anna-reveal--scale', { scale: 0.94 }, { duration: 0.85, ease: 'expo.out' });

    // Stagger grids
    gsap.utils.toArray('.anna-stagger').forEach(stagger);

    // Card hover lifts
    hoverLift('.anna-service-card', 8);

    // Recognition list — slide in from left with tight stagger
    var recItems = document.querySelectorAll('.anna-recognition__item');
    if (recItems.length) {
      prime(Array.from(recItems));
      gsap.fromTo(
        recItems,
        { autoAlpha: 0, x: -18 },
        {
          autoAlpha:  1,
          x:          0,
          duration:   0.7,
          ease:       'expo.out',
          clearProps: 'willChange,force3D',
          stagger: {
            amount: Math.min(recItems.length * 0.07, 0.55),
            ease:   'power1.inOut',
          },
          scrollTrigger: {
            trigger: '.anna-recognition__list',
            start:   'top 85%',
            once:    true,
          },
        }
      );
    }
  };
})();
