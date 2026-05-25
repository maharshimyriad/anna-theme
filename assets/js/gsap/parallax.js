/**
 * GSAP Parallax
 *
 * Subtle parallax movement on images and decorative elements.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  gsap.registerPlugin(ScrollTrigger);

  window.annaParallaxInit = function () {

    // Hero image subtle parallax
    var heroImg = document.querySelector('.anna-image-composition__primary img');
    if (heroImg) {
      gsap.to(heroImg, {
        y:    -30,
        ease: 'none',
        scrollTrigger: {
          trigger:  '.anna-hero',
          start:    'top top',
          end:      'bottom top',
          scrub:    1.5,
        },
      });
    }

    // Floating shapes parallax
    var shapes = document.querySelectorAll('.anna-about__shape, .anna-image-composition__shape');
    shapes.forEach(function (shape) {
      gsap.to(shape, {
        y:        -20,
        rotation: 5,
        ease:     'none',
        scrollTrigger: {
          trigger: shape.closest('section') || shape.parentElement,
          start:   'top bottom',
          end:     'bottom top',
          scrub:   2,
        },
      });
    });

    // CTA orbs parallax
    var ctaOrbs = document.querySelectorAll('.anna-cta__orb');
    ctaOrbs.forEach(function (orb, i) {
      gsap.to(orb, {
        y:    i % 2 === 0 ? -30 : 20,
        x:    i % 2 === 0 ? 15 : -10,
        ease: 'none',
        scrollTrigger: {
          trigger: '.anna-cta',
          start:   'top bottom',
          end:     'bottom top',
          scrub:   2,
        },
      });
    });
  };
})();
