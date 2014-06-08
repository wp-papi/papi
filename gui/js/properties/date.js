!function ($) {

  // Pikaday is used for PropertyDate
  if ('pikaday' in $.fn) {
    $('input[data-ptb-property="date"]').pikaday({
      format: 'YYYY-MM-DD',
      setDefaultDate:true
    });
  }

}(window.jQuery);