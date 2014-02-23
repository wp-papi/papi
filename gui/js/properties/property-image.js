(function ($) {
  
  // Use WordPress media uploader to set images on property image.
  if ($('[data-ptb-property="image"]').length) {
    $('body').on('click', '[data-ptb-property="image"]', function (e) {
      e.preventDefault();
      
      var $this = $(this)
        , $target = $this.prev();
      
      ptb.wp_media_editor($this, function (url) {
        $this.attr('src', url);
        $this.next().val(url);
      });
    });
  }
  
}(window.jQuery));