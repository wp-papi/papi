(function ($) {

  // Use WordPress media uploader to set images on property image.
  if ($('[data-ptb-property="image"]').length) {
    $('body').on('click', '[data-ptb-property="image"]', function (e) {
      e.preventDefault();

      var $this = $(this);

			// Todo: when removing image, remove style attribute

      Ptb.Utils.wp_media_editor($this, function (attachment) {
        if (Ptb.Utils.is_image(attachment.url)) {
					$this.attr('style', 'height:auto');
          $this.attr('src', attachment.url);
          $this.next().val(attachment.id);
        }
      });
    });
  }

}(window.jQuery));