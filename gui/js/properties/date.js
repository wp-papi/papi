(function ($) {

  // Property date

  papi.properties.date = {

    /**
     * Initialize property date.
     */

    init: function () {
      // Don't proceed if Pikaday is undefined.
      if (window.Pikaday === undefined) {
        return;
      }

      new Pikaday({
        field: $('input[data-papi-property="date"]')[0],
        format: 'YYYY-MM-DD',
        setDefaultDate: true
      });
    }
  };

})(jQuery);