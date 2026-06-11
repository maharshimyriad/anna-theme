/**
 * Mobile Menu Component
 *
 * - Slide-in panel open / close
 * - Accordion submenus with animated chevron
 * - Focus trap, Escape key, body scroll lock
 * - Closes on link tap (but not on toggle button tap)
 * - Closes on viewport resize to desktop
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  var toggle   = document.getElementById('mobile-menu-toggle');
  var nav      = document.getElementById('mobile-navigation');
  var closeBtn = document.getElementById('mobile-menu-close');
  var isOpen   = false;

  if (!toggle || !nav) return;

  /* ── Chevron SVG used inside each submenu toggle button ─────────────── */
  var CHEVRON_SVG =
    '<svg width="14" height="8" viewBox="0 0 14 8" fill="none" ' +
    'xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
    '<path d="M1 1L7 7L13 1" stroke="currentColor" stroke-width="1.8" ' +
    'stroke-linecap="round" stroke-linejoin="round"/></svg>';

  /* ── Open / close helpers ────────────────────────────────────────────── */
  function openMenu() {
    isOpen = true;
    nav.classList.add('is-open');
    nav.setAttribute('aria-hidden', 'false');
    toggle.setAttribute('aria-expanded', 'true');
    toggle.setAttribute('aria-label', 'Close menu');
    document.body.style.overflow = 'hidden';

    // Focus the close button so screen readers announce the panel.
    if (closeBtn) {
      closeBtn.focus();
    }
  }

  function closeMenu() {
    isOpen = false;
    nav.classList.remove('is-open');
    nav.setAttribute('aria-hidden', 'true');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Open menu');
    document.body.style.overflow = '';
    toggle.focus();
  }

  /* ── Toggle button ───────────────────────────────────────────────────── */
  toggle.addEventListener('click', function () {
    isOpen ? closeMenu() : openMenu();
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', closeMenu);
  }

  /* ── Escape key ──────────────────────────────────────────────────────── */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isOpen) closeMenu();
  });

  /* ── Submenu accordions ──────────────────────────────────────────────── */
  function setupSubmenus() {
    var submenus = nav.querySelectorAll('.sub-menu');

    submenus.forEach(function (submenu) {
      var parentItem = submenu.parentElement;
      var parentLink = parentItem ? parentItem.querySelector(':scope > a') : null;

      if (!parentLink) return;

      // Build the toggle button.
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'anna-mobile-nav__toggle';
      btn.setAttribute('aria-expanded', 'false');
      btn.setAttribute('aria-label', 'Expand submenu');
      btn.innerHTML = CHEVRON_SVG;

      // Insert button after the link text (inside the <a> flex row).
      parentLink.appendChild(btn);

      // Prevent tapping the parent link from navigating when it has children.
      parentLink.addEventListener('click', function (e) {
        // If the click target is the toggle button itself, do nothing here.
        if (e.target === btn || btn.contains(e.target)) return;
        // If parent link is a real URL let it navigate; otherwise toggle.
        var href = parentLink.getAttribute('href');
        if (!href || href === '#' || href === '') {
          e.preventDefault();
          toggleSubmenu(submenu, btn);
        }
      });

      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSubmenu(submenu, btn);
      });
    });
  }

  function toggleSubmenu(submenu, btn) {
    var expanded = submenu.classList.contains('is-expanded');

    if (expanded) {
      submenu.classList.remove('is-expanded');
      btn.setAttribute('aria-expanded', 'false');
      btn.setAttribute('aria-label', 'Expand submenu');
    } else {
      submenu.classList.add('is-expanded');
      btn.setAttribute('aria-expanded', 'true');
      btn.setAttribute('aria-label', 'Collapse submenu');
    }
  }

  /* ── Close when a child link is tapped ──────────────────────────────── */
  nav.addEventListener('click', function (e) {
    var link = e.target.closest('a');
    // Don't close if it's a parent link that just opened a submenu.
    if (link && !link.querySelector('.anna-mobile-nav__toggle')) {
      closeMenu();
    }
  });

  /* ── Focus trap ──────────────────────────────────────────────────────── */
  nav.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || !isOpen) return;

    var focusable = Array.prototype.slice.call(
      nav.querySelectorAll('a, button, input, [tabindex]:not([tabindex="-1"])')
    ).filter(function (el) {
      return !el.closest('.sub-menu:not(.is-expanded)');
    });

    if (!focusable.length) return;

    var first = focusable[0];
    var last  = focusable[focusable.length - 1];

    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  });

  /* ── Auto-close on desktop resize ───────────────────────────────────── */
  var mql = window.matchMedia('(min-width: 1025px)');
  mql.addEventListener('change', function (e) {
    if (e.matches && isOpen) closeMenu();
  });

  /* ── Init ────────────────────────────────────────────────────────────── */
  setupSubmenus();
})();
