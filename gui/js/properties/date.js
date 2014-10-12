(function ($) {

  // Property date

  papi.properties.date = {

    /**
     * Initialize property date.
     */

    init: function () {
      // Use Pikaday for property date.
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