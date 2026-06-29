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
   * Desktop dropdown — hover-intent with close delay so the gap
   * between the trigger and panel never accidentally closes it.
   * Keyboard (Enter / Space / Escape / Tab) also works.
   */
  (function () {
    var CLOSE_DELAY = 180; // ms — enough to cross the gap, not sluggish

    document.querySelectorAll('.anna-nav__item--has-children').forEach(function (item) {
      var link     = item.querySelector('.anna-nav__link--parent');
      var dropdown = item.querySelector('.anna-nav__dropdown');
      if (!link || !dropdown) return;

      var closeTimer = null;

      function openDropdown() {
        clearTimeout(closeTimer);
        // Close any other open dropdowns first.
        document.querySelectorAll('.anna-nav__item--has-children.is-open').forEach(function (other) {
          if (other !== item) {
            other.classList.remove('is-open');
            var otherLink = other.querySelector('.anna-nav__link--parent');
            if (otherLink) otherLink.setAttribute('aria-expanded', 'false');
          }
        });
        item.classList.add('is-open');
        link.setAttribute('aria-expanded', 'true');
      }

      function scheduleClose() {
        closeTimer = setTimeout(function () {
          item.classList.remove('is-open');
          link.setAttribute('aria-expanded', 'false');
        }, CLOSE_DELAY);
      }

      // Mouse enter on the whole item (trigger + panel).
      item.addEventListener('mouseenter', openDropdown);
      item.addEventListener('mouseleave', scheduleClose);

      // Cancel close if mouse re-enters the panel.
      dropdown.addEventListener('mouseenter', function () { clearTimeout(closeTimer); });
      dropdown.addEventListener('mouseleave', scheduleClose);

      // ── Keyboard ──────────────────────────────────────────────────────
      link.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          var isOpen = item.classList.contains('is-open');
          if (isOpen) {
            item.classList.remove('is-open');
            link.setAttribute('aria-expanded', 'false');
          } else {
            openDropdown();
            var firstChild = dropdown.querySelector('a');
            if (firstChild) firstChild.focus();
          }
        }
        if (e.key === 'Escape') {
          item.classList.remove('is-open');
          link.setAttribute('aria-expanded', 'false');
          link.focus();
        }
      });

      // Close on Tab-away.
      item.addEventListener('focusout', function () {
        setTimeout(function () {
          if (!item.contains(document.activeElement)) {
            item.classList.remove('is-open');
            link.setAttribute('aria-expanded', 'false');
          }
        }, 50);
      });

      // Close when clicking outside.
      document.addEventListener('click', function (e) {
        if (!item.contains(e.target)) {
          item.classList.remove('is-open');
          link.setAttribute('aria-expanded', 'false');
        }
      });
    });
  })();

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


document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a[onclick*="_rplg_popup"]').forEach(function (link) {
        const onclick = link.getAttribute('onclick');

        const match = onclick.match(/_rplg_popup\('([^']+)'/);

        if (match && match[1]) {
            link.href = match[1];
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
        }
    });
});