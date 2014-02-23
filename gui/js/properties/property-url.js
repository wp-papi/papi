(function ($) {
  
  // Use WordPress media uploader to set files on property url.
  if ($('[data-ptb-action="mediauploader"]').length) {
    $('body').on('click', '[data-ptb-action="mediauploader"]', function (e) {
      e.preventDefault();
      
      var attch = wp.media.editor.send.attachment
        , $this = $(this)
        , $target = $this.prev();
      
      wp.media.editor.send.attachment = function (props, attachment) {
        $target.val(attachment.url);
      };
      
      wp.media.editor.open($this);
    });
  }
  
}(window.jQuery));