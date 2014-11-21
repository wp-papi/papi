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

      var $prop = $('input.papi-property-date');

      new Pikaday({
        field: $prop[0],
        format: $prop.data().format,
        setDefaultDate: true
      });
    }
  };

})(jQuery);
