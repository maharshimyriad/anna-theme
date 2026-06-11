/**
 * Mobile Menu Component JS
 *
 * Toggle, focus trapping, escape key, body scroll lock.
 * Handles nested menu accordion expansion.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  var toggle  = document.getElementById('mobile-menu-toggle');
  var nav     = document.getElementById('mobile-navigation');
  var closeBtn = document.getElementById('mobile-menu-close');
  var isOpen  = false;

  if (!toggle || !nav) return;

  function openMenu() {
    isOpen = true;
    toggle.setAttribute('aria-expanded', 'true');
    toggle.setAttribute('aria-label', 'Close menu');
    nav.classList.add('is-open');
    nav.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Focus first link.
    var firstLink = nav.querySelector('a, button');
    if (firstLink) firstLink.focus();
  }

  function closeMenu() {
    isOpen = false;
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Open menu');
    nav.classList.remove('is-open');
    nav.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    toggle.focus();
  }

  toggle.addEventListener('click', function () {
    isOpen ? closeMenu() : openMenu();
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', function () {
      closeMenu();
    });
  }

  // Close on Escape.
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isOpen) {
      closeMenu();
    }
  });

  // Handle nested menu items with submenus.
  function setupSubmenus() {
    var lists = nav.querySelectorAll('.anna-mobile-nav__list .anna-mobile-nav__list');
    
    lists.forEach(function (submenu) {
      var parentItem = submenu.parentElement;
      var parentLink = parentItem.querySelector('.anna-mobile-nav__link');
      
      if (!parentLink) return;
      
      // Create toggle button.
      var toggleBtn = document.createElement('button');
      toggleBtn.type = 'button';
      toggleBtn.className = 'anna-mobile-nav__toggle';
      toggleBtn.setAttribute('aria-expanded', 'false');
      toggleBtn.setAttribute('aria-label', 'Expand submenu');
      
      // Append toggle to parent link.
      parentLink.appendChild(toggleBtn);
      
      // Toggle submenu on button click.
      toggleBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        var isExpanded = submenu.classList.contains('is-expanded');
        
        if (isExpanded) {
          submenu.classList.remove('is-expanded');
          toggleBtn.classList.remove('is-expanded');
          toggleBtn.setAttribute('aria-expanded', 'false');
        } else {
          submenu.classList.add('is-expanded');
          toggleBtn.classList.add('is-expanded');
          toggleBtn.setAttribute('aria-expanded', 'true');
        }
      });
    });
  }

  // Close when clicking a nav link (mobile) — but not toggle buttons.
  nav.addEventListener('click', function (e) {
    if (e.target.closest('a')) {
      closeMenu();
    }
  });

  // Focus trap within mobile nav.
  nav.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || !isOpen) return;

    var focusable = nav.querySelectorAll('a, button, input, [tabindex]:not([tabindex="-1"])');
    var first     = focusable[0];
    var last      = focusable[focusable.length - 1];

    if (e.shiftKey) {
      if (document.activeElement === first) {
        e.preventDefault();
        last.focus();
      }
    } else {
      if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });

  // Close on resize above breakpoint.
  var mql = window.matchMedia('(min-width: 1025px)');
  mql.addEventListener('change', function (e) {
    if (e.matches && isOpen) closeMenu();
  });

  // Initialize submenus.
  setupSubmenus();
})();
