/**
 * Main Theme JS
 *
 * Entry point. All component scripts are loaded as
 * separate deferred modules. This file handles any
 * orchestration or page-level logic.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  /**
   * Smooth scroll for anchor links.
   */
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var href   = this.getAttribute('href');
      if (href === '#') return;

      var target = document.querySelector(href);
      if (!target) return;

      e.preventDefault();
      var headerH = document.getElementById('site-header');
      var offset  = headerH ? headerH.offsetHeight + 16 : 80;
      var pos     = target.getBoundingClientRect().top + window.scrollY - offset;

      window.scrollTo({ top: pos, behavior: 'smooth' });

      // Update focus for accessibility.
      target.setAttribute('tabindex', '-1');
      target.focus({ preventScroll: true });
    });
  });

  /**
   * Dropdown keyboard navigation.
   */
  document.querySelectorAll('.anna-nav__item--has-children').forEach(function (item) {
    var link     = item.querySelector('.anna-nav__link--parent');
    var dropdown = item.querySelector('.anna-nav__dropdown');
    if (!link || !dropdown) return;

    // Toggle on Enter/Space
    link.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        var expanded = link.getAttribute('aria-expanded') === 'true';
        link.setAttribute('aria-expanded', !expanded);
        item.classList.toggle('is-open');

        if (!expanded) {
          var firstChild = dropdown.querySelector('a');
          if (firstChild) firstChild.focus();
        }
      }

      if (e.key === 'Escape') {
        link.setAttribute('aria-expanded', 'false');
        item.classList.remove('is-open');
        link.focus();
      }
    });

    // Close dropdown when leaving with Tab.
    item.addEventListener('focusout', function (e) {
      setTimeout(function () {
        if (!item.contains(document.activeElement)) {
          link.setAttribute('aria-expanded', 'false');
          item.classList.remove('is-open');
        }
      }, 50);
    });
  });

  /**
   * Open all "Book a Discovery Call" links in a new tab.
   * The URL is injected via wp_localize_script as annaTheme.discoveryCallUrl.
   */
  (function () {
    var discoveryUrl = (window.annaTheme && window.annaTheme.discoveryCallUrl)
      ? window.annaTheme.discoveryCallUrl.replace(/\/$/, '')
      : null;

    if (!discoveryUrl) return;

    document.querySelectorAll('a[href]').forEach(function (link) {
      var href = link.getAttribute('href').replace(/\/$/, '');
      if (href === discoveryUrl) {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
      }
    });
  })();

  /**
   * Year auto-update in copyright if placeholder found.
   */
  var copyrightEl = document.querySelector('.anna-footer__copyright');
  if (copyrightEl && copyrightEl.textContent.includes('{{year}}')) {
    copyrightEl.textContent = copyrightEl.textContent.replace('{{year}}', new Date().getFullYear());
  }

  /**
   * Lazy load attribute fix for images loaded early.
   */
  document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
    if (img.complete && img.naturalHeight === 0) {
      img.style.minHeight = 'auto';
    }
  });

})();
