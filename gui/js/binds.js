(function ($) {

  // Use WordPress media uploader to set files on property url
  // when `data-papi-action` has the action `mediauploader`
  $('body').on('click', '[data-papi-action="mediauploader"]', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $target = $this.prev();

    act.Utils.wp_media_editor($this, $target);
  });

  // Use Pikaday for property date.
  if (window.Pikaday !== undefined) {
    new Pikaday({
      field: $('input.papi-property-date')[0],
      format: 'YYYY-MM-DD',
      setDefaultDate: true
    });
  }

})(jQuery);
