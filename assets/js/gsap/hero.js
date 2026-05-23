/**
 * GSAP Hero Animations
 *
 * Timeline-based entrance animation for the hero section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  if (typeof gsap === 'undefined') return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  window.annaHeroInit = function () {
    var hero = document.getElementById('hero');
    if (!hero) return;

    var tl = gsap.timeline({
      defaults: { ease: 'power3.out' },
      delay: 0.3,
    });

    // Eyebrow
    var eyebrow = hero.querySelector('.anna-eyebrow');
    if (eyebrow) {
      tl.from(eyebrow, { y: 20, opacity: 0, duration: 0.6 });
    }

    // Heading — word by word
    var heading = hero.querySelector('.anna-hero__heading');
    if (heading) {
      var text = heading.innerHTML;
      var words = text.split(/(\s+)/);
      heading.innerHTML = words.map(function (word) {
        if (word.trim() === '') return word;
        return '<span class="anna-hero__word" style="display:inline-block;overflow:hidden;"><span style="display:inline-block;">' + word + '</span></span>';
      }).join('');

      var wordSpans = heading.querySelectorAll('.anna-hero__word > span');
      tl.from(wordSpans, {
        y:        '100%',
        opacity:  0,
        duration: 0.7,
        stagger:  0.06,
      }, '-=0.3');
    }

    // Description
    var desc = hero.querySelector('.anna-hero__description');
    if (desc) {
      tl.from(desc, { y: 24, opacity: 0, duration: 0.6 }, '-=0.3');
    }

    // CTA buttons
    var ctas = hero.querySelector('.anna-hero__ctas');
    if (ctas) {
      tl.from(ctas.children, {
        y:       20,
        opacity: 0,
        duration: 0.5,
        stagger: 0.12,
      }, '-=0.2');
    }

    // Trust line
    var trust = hero.querySelector('.anna-hero__trust');
    if (trust) {
      tl.from(trust, { y: 16, opacity: 0, duration: 0.5 }, '-=0.1');
    }

    // Visual side
    var visual = hero.querySelector('.anna-hero__visual');
    if (visual) {
      var img = visual.querySelector('.anna-image-composition__primary');
      if (img) {
        tl.from(img, { x: 60, opacity: 0, duration: 0.9, ease: 'power2.out' }, '-=0.8');
      }
    }

    // Stat cards — stagger
    var stats = hero.querySelectorAll('.anna-hero__stat');
    if (stats.length) {
      tl.from(stats, {
        scale:    0.8,
        opacity:  0,
        duration: 0.5,
        stagger:  0.15,
        ease:     'back.out(1.7)',
      }, '-=0.5');
    }
  };
})();
