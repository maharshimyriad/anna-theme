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
      updateAdminRepeaterCollapseLabel(repeater);
    });

    $(document).on('click', '[data-anna-repeater-remove="true"]', function (e) {
      e.preventDefault();
      $(this).closest('[data-anna-repeater-row="true"]').remove();
      updateAdminRepeaterCollapseLabel($(this).closest('[data-anna-repeater]'));
    });

    function updateAdminRepeaterCollapseLabel(repeater) {
      var collapse = repeater.closest('.anna-repeater-collapse');
      if (!collapse.length) {
        return;
      }

      var count = repeater.find('[data-anna-repeater-row="true"]').length;
      var toggle = collapse.find('[data-anna-repeater-collapse-toggle="true"]').first();
      var expanded = toggle.attr('aria-expanded') === 'true';
      var showText = 'Show all cards (' + count + ')';
      var hideText = 'Hide all cards (' + count + ')';

      toggle.find('.anna-repeater-collapse__label').text(expanded ? hideText : showText);
    }

    $(document).on('click', '[data-anna-repeater-collapse-toggle="true"]', function (e) {
      e.preventDefault();

      var toggle = $(this);
      var panel = toggle.closest('.anna-repeater-collapse').find('[data-anna-repeater-collapse-panel="true"]').first();
      var expanded = toggle.attr('aria-expanded') === 'true';
      var repeater = panel.find('[data-anna-repeater]').first();

      toggle.attr('aria-expanded', expanded ? 'false' : 'true');
      panel.toggleClass('is-collapsed', expanded);

      if (repeater.length) {
        updateAdminRepeaterCollapseLabel(repeater);
      }
    });

    $('[data-anna-repeater]').each(function () {
      updateAdminRepeaterCollapseLabel($(this));
    });
  });
})(jQuery);
