(function ($) {

  // Use WordPress media uploader to set files on property url
  // when `data-ptb-action` has the action `mediauploader`
  $('body').on('click', '[data-ptb-action="mediauploader"]', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $target = $this.prev();

    ptb.Utils.wp_media_editor($this, $target);
  });

  // Use Select2 for property dropdown list.
  if ('select2' in $.fn) {
    $('select.ptb-property-dropdown').select2();
  }

  // Use Pikaday for property date.
  if (window.Pikaday !== undefined) {
    new Pikaday({
      field: $('input.ptb-property-date')[0],
      format: 'YYYY-MM-DD',
      setDefaultDate: true
    });
  }

})(jQuery);