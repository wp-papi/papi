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

      var $prop    = $('input.papi-property-date'),
          settings = $prop.data().settings;

      settings['field'] = $prop[0];
console.log(settings);
      new Pikaday(settings);
    }
  };

})(jQuery);
