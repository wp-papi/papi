(function ($) {

  // Property datetime

  papi.properties.datetime = {

    /**
     * Initialize property date.
     */

    init: function () {
      // Don't proceed if Pikaday is undefined.
      if (window.Pikaday === undefined) {
        return;
      }

      var $prop    = $('input.papi-property-datetime'),
          settings = $prop.data().settings;

      settings['field'] = $prop[0];

      new Pikaday(settings);
    }
  };

})(jQuery);
