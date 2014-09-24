(function ($) {

  // Use WordPress media uploader to set files on property url
  // when `data-papi-action` has the action `mediauploader`
  $('body').on('click', '[data-papi-action="mediauploader"]', function (e) {
    e.preventDefault();

    var $target = $(this).prev();

    papi.Utils.wpMediaEditor($target);
  });

  // Use Pikaday for property date.
  if (window.Pikaday !== undefined) {
    new Pikaday({
      field: $('input[data-papi-property="date"]')[0],
      format: 'YYYY-MM-DD',
      setDefaultDate: true
    });
  }

})(jQuery);
