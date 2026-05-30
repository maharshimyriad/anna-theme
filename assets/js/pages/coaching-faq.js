/**
 * Coaching page FAQ accordion.
 */
(function () {
  'use strict';

  function initCoachingFaq() {
    var root = document.querySelector('[data-anna-coaching-faq]');
    if (!root) {
      return;
    }

    root.addEventListener('click', function (event) {
      var trigger = event.target.closest('.anna-coaching-page-faq__trigger');
      if (!trigger || !root.contains(trigger)) {
        return;
      }

      var item = trigger.closest('.anna-coaching-page-faq__item');
      var panel = item ? item.querySelector('.anna-coaching-page-faq__panel') : null;
      if (!item || !panel) {
        return;
      }

      var isOpen = item.classList.contains('is-open');

      root.querySelectorAll('.anna-coaching-page-faq__item.is-open').forEach(function (openItem) {
        if (openItem === item) {
          return;
        }
        openItem.classList.remove('is-open');
        var openTrigger = openItem.querySelector('.anna-coaching-page-faq__trigger');
        var openPanel = openItem.querySelector('.anna-coaching-page-faq__panel');
        if (openTrigger) {
          openTrigger.setAttribute('aria-expanded', 'false');
        }
        if (openPanel) {
          openPanel.hidden = true;
        }
      });

      if (isOpen) {
        item.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        panel.hidden = true;
        return;
      }

      item.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
      panel.hidden = false;
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCoachingFaq);
  } else {
    initCoachingFaq();
  }
})();
