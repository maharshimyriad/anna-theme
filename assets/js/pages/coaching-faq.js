/**
 * Coaching page FAQ accordion.
 */
(function () {
  'use strict';

  function initFaqRoot(root) {
    root.addEventListener('click', function (event) {
      var trigger = event.target.closest(
        '.anna-coaching-page-faq__trigger, .anna-oasis-page-faq__trigger'
      );
      if (!trigger || !root.contains(trigger)) {
        return;
      }

      var item = trigger.closest('.anna-coaching-page-faq__item, .anna-oasis-page-faq__item');
      var panel = item
        ? item.querySelector('.anna-coaching-page-faq__panel, .anna-oasis-page-faq__panel')
        : null;
      if (!item || !panel) {
        return;
      }

      var isOpen = item.classList.contains('is-open');
      var itemSelector = item.classList.contains('anna-oasis-page-faq__item')
        ? '.anna-oasis-page-faq__item.is-open'
        : '.anna-coaching-page-faq__item.is-open';
      var triggerSelector = item.classList.contains('anna-oasis-page-faq__item')
        ? '.anna-oasis-page-faq__trigger'
        : '.anna-coaching-page-faq__trigger';
      var panelSelector = item.classList.contains('anna-oasis-page-faq__item')
        ? '.anna-oasis-page-faq__panel'
        : '.anna-coaching-page-faq__panel';

      root.querySelectorAll(itemSelector).forEach(function (openItem) {
        if (openItem === item) {
          return;
        }
        openItem.classList.remove('is-open');
        var openTrigger = openItem.querySelector(triggerSelector);
        var openPanel = openItem.querySelector(panelSelector);
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

  function initCoachingFaq() {
    document
      .querySelectorAll('[data-anna-coaching-faq], [data-anna-oasis-faq]')
      .forEach(initFaqRoot);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCoachingFaq);
  } else {
    initCoachingFaq();
  }
})();
