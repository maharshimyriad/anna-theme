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

  function createReveal(selector, fromVars) {
    gsap.utils.toArray(selector).forEach(function (el) {
      gsap.set(el, Object.assign({ willChange: 'transform, opacity' }, fromVars));
      gsap.to(el, {
        x: typeof fromVars.x === 'number' ? 0 : undefined,
        y: typeof fromVars.y === 'number' ? 0 : undefined,
        scale: typeof fromVars.scale === 'number' ? 1 : undefined,
        autoAlpha: 1,
        duration: fromVars.duration || 0.8,
        ease: fromVars.ease || 'power2.out',
        clearProps: 'willChange',
        scrollTrigger: {
          trigger: el,
          start: 'top 85%',
          once: true,
        },
      });
    });
  }

  window.annaScrollTriggersInit = function () {
    createReveal('.anna-reveal', { y: 40, autoAlpha: 0, duration: 0.8, ease: 'power2.out' });
    createReveal('.anna-reveal--left', { x: -40, autoAlpha: 0, duration: 0.8, ease: 'power2.out' });
    createReveal('.anna-reveal--right', { x: 40, autoAlpha: 0, duration: 0.8, ease: 'power2.out' });
    createReveal('.anna-reveal--scale', { scale: 0.92, autoAlpha: 0, duration: 0.7, ease: 'power2.out' });

    gsap.utils.toArray('.anna-stagger').forEach(function (container) {
      var children = container.children;
      if (!children.length) return;

      gsap.set(children, {
        y: 24,
        autoAlpha: 0,
        willChange: 'transform, opacity',
      });

      gsap.to(children, {
        y: 0,
        autoAlpha: 1,
        duration: 0.7,
        stagger: {
          amount: Math.min(children.length * 0.08, 0.6),
          ease: 'power1.inOut',
        },
        ease: 'power3.out',
        clearProps: 'willChange',
        scrollTrigger: {
          trigger: container,
          start: 'top 88%',
          once: true,
        },
      });
    });

    document.querySelectorAll('.anna-service-card').forEach(function (card) {
      card.addEventListener('mouseenter', function () {
        gsap.to(card, { y: -8, duration: 0.3, ease: 'power2.out' });
      });
      card.addEventListener('mouseleave', function () {
        gsap.to(card, { y: 0, duration: 0.4, ease: 'power2.inOut' });
      });
    });

    var recItems = document.querySelectorAll('.anna-recognition__item');
    if (recItems.length) {
      gsap.set(recItems, {
        x: -20,
        autoAlpha: 0,
      });

      gsap.to(recItems, {
        x: 0,
        autoAlpha: 1,
        duration: 0.5,
        stagger: 0.08,
        ease: 'power2.out',
        scrollTrigger: {
          trigger: '.anna-recognition__list',
          start: 'top 80%',
          once: true,
        },
      });
    }
  };
})();
