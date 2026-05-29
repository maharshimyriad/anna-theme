/**
 * Admin Settings JS
 *
 * Handles color picker init, media uploads, and UI interactions.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function ($) {
  'use strict';

  $(document).ready(function () {
    var toast = $('[data-anna-toast="true"]');

    if (toast.length) {
      window.setTimeout(function () {
        toast.addClass('is-visible');
      }, 80);

      window.setTimeout(function () {
        toast.removeClass('is-visible');
      }, 3600);
    }

    // Init color pickers.
    if ($.fn.wpColorPicker) {
      $('.anna-color-picker').wpColorPicker();
    }

    // Media upload handler.
    $(document).on('click', '.anna-media-upload-btn', function (e) {
      e.preventDefault();

      var button    = $(this);
      var targetId  = button.data('target');
      var previewId = button.data('preview');

      var frame = wp.media({
        title: 'Select Image',
        library: { type: 'image' },
        button: { text: 'Use Image' },
        multiple: false,
      });

      frame.on('select', function () {
        var attachment = frame.state().get('selection').first().toJSON();
        var thumbUrl   = attachment.sizes && attachment.sizes.thumbnail
          ? attachment.sizes.thumbnail.url
          : attachment.url;

        $('#' + targetId).val(attachment.id);
        $('#' + previewId).html(
          '<img src="' + thumbUrl + '" alt="" style="max-width:150px;height:auto;border-radius:8px;">'
        );
        button.siblings('.anna-media-remove-btn').show();
      });

      frame.open();
    });

    // Media remove handler.
    $(document).on('click', '.anna-media-remove-btn', function (e) {
      e.preventDefault();

      var button    = $(this);
      var targetId  = button.data('target');
      var previewId = button.data('preview');

      $('#' + targetId).val('');
      $('#' + previewId).html('');
      button.hide();
    });

    // Simple repeater (About qualifications).
    $(document).on('click', '[data-anna-repeater-add="true"]', function (e) {
      e.preventDefault();

      var repeater = $(this).closest('[data-anna-repeater]');
      var rowsWrap = repeater.find('[data-anna-repeater-rows="true"]').first();
      var template = repeater.find('[data-anna-repeater-template="true"]').first();

      if (!template.length || !rowsWrap.length) {
        return;
      }

      var index = rowsWrap.find('[data-anna-repeater-row="true"]').length;
      var html = template.html().replace(/__INDEX__/g, String(index));
      rowsWrap.append(html);

      var wrap = repeater.closest('.anna-repeater-collapse');
      if (wrap.length) {
        updateAdminRepeaterCollapseLabel(wrap);
      }
    });

    $(document).on('click', '[data-anna-repeater-remove="true"]', function (e) {
      e.preventDefault();
      var repeater = $(this).closest('[data-anna-repeater]');
      $(this).closest('[data-anna-repeater-row="true"]').remove();

      var wrap = repeater.closest('.anna-repeater-collapse');
      if (wrap.length) {
        updateAdminRepeaterCollapseLabel(wrap);
      }
    });

    function getAdminCollapseWrap(toggleBtn) {
      return toggleBtn.closest('.anna-repeater-collapse');
    }

    function getAdminCollapsePanel(wrap) {
      return wrap.find('.anna-repeater-collapse__panel').first();
    }

    function updateAdminRepeaterCollapseLabel(wrap) {
      if (!wrap || !wrap.length) {
        return;
      }

      var toggle = wrap.find('.anna-repeater-collapse__toggle').first();
      var panel = getAdminCollapsePanel(wrap);
      var repeater = panel.find('[data-anna-repeater]').first();
      var count = repeater.find('[data-anna-repeater-row="true"]').length;
      var expanded = toggle.attr('aria-expanded') === 'true';
      var showText = 'Show all cards (' + count + ')';
      var hideText = 'Hide all cards (' + count + ')';

      toggle.find('.anna-repeater-collapse__label').text(expanded ? hideText : showText);
    }

    function initAdminRepeaterCollapses() {
      $('.anna-repeater-collapse').each(function () {
        var wrap = $(this);
        var toggle = wrap.find('.anna-repeater-collapse__toggle').first();
        var panel = getAdminCollapsePanel(wrap);

        if (!toggle.length || !panel.length) {
          return;
        }

        var expanded = toggle.attr('aria-expanded') === 'true';
        if (expanded) {
          panel.removeClass('is-collapsed').show();
        } else {
          panel.addClass('is-collapsed').hide();
        }

        updateAdminRepeaterCollapseLabel(wrap);
      });
    }

    $(document).on('click', '.anna-repeater-collapse__toggle', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var toggle = $(this).closest('.anna-repeater-collapse__toggle');
      if (!toggle.length) {
        toggle = $(this);
      }

      var wrap = getAdminCollapseWrap(toggle);
      var panel = getAdminCollapsePanel(wrap);
      var collapsed = panel.hasClass('is-collapsed');

      if (collapsed) {
        toggle.attr('aria-expanded', 'true');
        panel.removeClass('is-collapsed').slideDown(200);
      } else {
        toggle.attr('aria-expanded', 'false');
        panel.slideUp(200, function () {
          panel.addClass('is-collapsed');
        });
      }

      updateAdminRepeaterCollapseLabel(wrap);
    });

    initAdminRepeaterCollapses();
  });
})(jQuery);
