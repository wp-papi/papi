(function ($) {

  // Use WordPress media uploader to set files on property url.
  if ($('[data-ptb-action="mediauploader"]').length) {
    $('body').on('click', '[data-ptb-action="mediauploader"]', function (e) {
      e.preventDefault();

      var $this = $(this)
        , $target = $this.prev();

      Ptb.Utils.wp_media_editor($this, $target);
    });
  }

}(window.jQuery));