/**
 * GSAP Hero Animations
 *
 * Timeline-based entrance for the homepage hero.
 * Overlapping tweens + expo.out give a polished, high-end feel.
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
      defaults: { ease: 'expo.out', force3D: true },
      delay:    0.15,
      onComplete: function () {
        // Release GPU layer after entrance is done
        gsap.set([eyebrow, heading, desc, ctas, trust], { clearProps: 'force3D,willChange' });
      },
    });

    var eyebrow = hero.querySelector('.anna-hero__overline, .anna-eyebrow');
    var heading = hero.querySelector('.anna-hero__heading');
    var desc    = hero.querySelector('.anna-hero__description');
    var ctas    = hero.querySelector('.anna-hero__ctas');
    var trust   = hero.querySelector('.anna-hero__trust');
    var visual  = hero.querySelector('.anna-hero__visual');
    var stats   = hero.querySelectorAll('.anna-hero__stat');

    // Eyebrow fades up first
    if (eyebrow) {
      tl.from(eyebrow, { y: 14, autoAlpha: 0, duration: 0.7 });
    }

    // Heading — word-by-word clip reveal
    if (heading) {
      var rawHTML = heading.innerHTML;
      var words   = rawHTML.split(/(\s+)/);
      heading.innerHTML = words.map(function (chunk) {
        if (chunk.trim() === '') return chunk;
        return (
          '<span class="anna-hero__word" style="display:inline-block;overflow:hidden;vertical-align:bottom;">' +
          '<span class="anna-hero__word-inner" style="display:inline-block;">' +
          chunk +
          '</span></span>'
        );
      }).join('');

      var inners = heading.querySelectorAll('.anna-hero__word-inner');
      tl.from(
        inners,
        {
          yPercent:  105,
          autoAlpha: 0,
          duration:  0.75,
          stagger:   { amount: 0.4, ease: 'power2.inOut' },
        },
        eyebrow ? '-=0.45' : 0
      );
    }

    // Description
    if (desc) {
      tl.from(desc, { y: 20, autoAlpha: 0, duration: 0.65 }, '-=0.4');
    }

    // CTA buttons — stagger in
    if (ctas && ctas.children.length) {
      tl.from(
        ctas.children,
        {
          y:        16,
          autoAlpha: 0,
          duration:  0.55,
          stagger:   0.1,
        },
        '-=0.35'
      );
    }

    // Trust line
    if (trust) {
      tl.from(trust, { y: 12, autoAlpha: 0, duration: 0.5 }, '-=0.2');
    }

    // Visual / image — slides in from right, slight scale
    if (visual) {
      var img = visual.querySelector('.anna-image-composition__primary');
      if (img) {
        tl.from(
          img,
          { x: 50, scale: 0.97, autoAlpha: 0, duration: 1.0, ease: 'expo.out' },
          heading ? '-=0.85' : '-=0.5'
        );
      }
    }

    // Stat cards — spring up with back easing
    if (stats.length) {
      tl.from(
        stats,
        {
          y:         22,
          scale:     0.88,
          autoAlpha: 0,
          duration:  0.55,
          stagger:   { amount: 0.25, ease: 'power1.inOut' },
          ease:      'back.out(1.5)',
        },
        '-=0.55'
      );
    }
  };
})();
