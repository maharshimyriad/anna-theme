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
  });
})(jQuery);
