/**
 * GSAP Animation Controller
 *
 * Central orchestrator that initializes all GSAP modules.
 * Respects prefers-reduced-motion and admin animation toggle.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  if (typeof gsap === 'undefined') return;

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var config = window.annaTheme || {};
  var animationsEnabled = config.animationsEnabled !== false;

  if (prefersReducedMotion || !animationsEnabled) {
    // Remove reveal initial states so content is visible.
    document.querySelectorAll('.anna-reveal, .anna-reveal--left, .anna-reveal--right, .anna-reveal--scale, .anna-stagger').forEach(function (el) {
      el.classList.add('is-visible');
    });
    return;
  }

  // Set animation speed multiplier.
  var speedMap = { fast: 0.6, normal: 1, slow: 1.4, 'very-slow': 1.8 };
  var speedMultiplier = speedMap[config.animationSpeed] || 1;

  gsap.defaults({
    duration: 0.7 * speedMultiplier,
    ease: 'power2.out',
  });

  // Initialize all animation modules.
  function init() {
    try {
      if (typeof window.annaHeroInit === 'function') {
        window.annaHeroInit();
      }
      if (typeof window.annaScrollTriggersInit === 'function') {
        window.annaScrollTriggersInit();
      }
      if (typeof window.annaParallaxInit === 'function') {
        window.annaParallaxInit();
      }
    } catch (error) {
      document.querySelectorAll('.anna-reveal, .anna-reveal--left, .anna-reveal--right, .anna-reveal--scale, .anna-stagger').forEach(function (el) {
        el.classList.add('is-visible');
      });
      console.error('Anna animation init failed:', error);
    }
  }

  // Wait for DOM ready.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // NOTE: AJAX reinit is handled by transition-manager.js which calls
  // annaHeroInit, annaScrollTriggersInit, annaParallaxInit directly.
  // Do NOT add a pageLoaded listener here — it would double-init and
  // corrupt the hero heading word-split.
})();
