(function ($) {

  // Property datetime

  papi.properties.datetime = {

    /**
     * Initialize property date.
     */

    init: function () {
      this.pikaday($('.inside > .papi-table > tbody > tr > td > input.papi-property-datetime'));
      this.pikaday($('.papi-table .papi-table:not(.papi-table-template) input.papi-property-datetime'));
    },

    pikaday: function ($prop) {
      // Don't proceed if Pikaday is undefined.
      if (window.Pikaday === undefined) {
        return;
      }

      if (!$prop.length) {
        return;
      }

      var settings = $prop.data().settings;

      settings['field'] = $prop[0];

      new Pikaday(settings);
    },

    /**
     * Initialize pikaday field when added to repeater.
     *
     * @param object $this
     */

     updateSelect: function ($this) {
      this.pikaday($this.prev());
    }
  };

  // Events

  $(document).on('papi/property/repeater/added', '[value="datetime"]', function (e) {
    e.preventDefault();

    papi.properties.datetime.updateSelect($(this));
  });


})(jQuery);
