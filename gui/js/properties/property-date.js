!function ($) {

  // Property Date with Pikaday date picker.
  if ($('input[data-ptb-property="date"]').length) {
    $('input[data-ptb-property="date"]').pikaday({
      format: 'YYYY-MM-DD',
      setDefaultDate:true
    });
  }

}(window.jQuery);