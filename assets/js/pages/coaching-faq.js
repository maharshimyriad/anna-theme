/**
 * FAQ accordion — smooth height animation.
 *
 * Handles both the Coaching page (.anna-coaching-page-faq__*)
 * and the Oasis page (.anna-oasis-page-faq__*) accordions.
 *
 * Uses scrollHeight so panels expand to exactly the right size
 * regardless of content length. No max-height hacks needed.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  /* ── Smooth panel helpers ──────────────────────────────────────────── */

  function openPanel(panel) {
    panel.style.height = '0px';
    panel.getBoundingClientRect(); // force reflow
    panel.style.height = panel.scrollHeight + 'px';

    panel.addEventListener('transitionend', function onEnd(e) {
      if (e.propertyName !== 'height') return;
      panel.removeEventListener('transitionend', onEnd);
      panel.style.height = 'auto';
    });
  }

  function closePanel(panel) {
    panel.style.height = panel.scrollHeight + 'px';
    panel.getBoundingClientRect(); // force reflow
    panel.style.height = '0px';
  }

  /* ── Selector helpers ─────────────────────────────────────────────── */

  function getSelectors(item) {
    var isOasis = item.classList.contains('anna-oasis-page-faq__item');
    return {
      openItems:  isOasis ? '.anna-oasis-page-faq__item.is-open'    : '.anna-coaching-page-faq__item.is-open',
      trigger:    isOasis ? '.anna-oasis-page-faq__trigger'          : '.anna-coaching-page-faq__trigger',
      panel:      isOasis ? '.anna-oasis-page-faq__panel'            : '.anna-coaching-page-faq__panel',
    };
  }

  /* ── Per-grid initialiser ─────────────────────────────────────────── */

  function initGrid(grid) {
    // Set initial heights so CSS transition has a proper starting value.
    grid.querySelectorAll(
      '.anna-coaching-page-faq__item, .anna-oasis-page-faq__item'
    ).forEach(function (item) {
      var panel = item.querySelector(
        '.anna-coaching-page-faq__panel, .anna-oasis-page-faq__panel'
      );
      if (!panel) return;
      panel.style.height = item.classList.contains('is-open') ? 'auto' : '0px';
    });

    // Delegate click to the grid so dynamically added items work too.
    grid.addEventListener('click', function (e) {
      var trigger = e.target.closest(
        '.anna-coaching-page-faq__trigger, .anna-oasis-page-faq__trigger'
      );
      if (!trigger || !grid.contains(trigger)) return;

      var item = trigger.closest(
        '.anna-coaching-page-faq__item, .anna-oasis-page-faq__item'
      );
      var panel = item
        ? item.querySelector('.anna-coaching-page-faq__panel, .anna-oasis-page-faq__panel')
        : null;
      if (!item || !panel) return;

      var sel    = getSelectors(item);
      var isOpen = item.classList.contains('is-open');

      // Close all other open items in this grid.
      grid.querySelectorAll(sel.openItems).forEach(function (openItem) {
        if (openItem === item) return;
        openItem.classList.remove('is-open');
        var openTrigger = openItem.querySelector(sel.trigger);
        var openPanel   = openItem.querySelector(sel.panel);
        if (openTrigger) openTrigger.setAttribute('aria-expanded', 'false');
        if (openPanel)   closePanel(openPanel);
      });

      // Toggle clicked item.
      if (isOpen) {
        item.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        closePanel(panel);
      } else {
        item.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        openPanel(panel);
      }
    });
  }

  /* ── Boot ─────────────────────────────────────────────────────────── */

  function init() {
    document
      .querySelectorAll('[data-anna-coaching-faq], [data-anna-oasis-faq]')
      .forEach(initGrid);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
