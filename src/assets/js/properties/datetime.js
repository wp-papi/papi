(function ($) {

  'use strict';

  // Property datetime

  papi.properties.datetime = {

    /**
     * Initialize property date.
     */

    init: function () {
      this.pikaday($('.inside > .papi-table > tbody > tr > td > input.papi-property-datetime'));
      this.pikaday($('.papi-table .papi-table:not(.papi-table-template) input.papi-property-datetime'));
    },

    /**
     * Initialize Pikaday.
     *
     * @param {object} $prop
     */

    pikaday: function ($props) {
      // Don't proceed if Pikaday is undefined.
      if (window.Pikaday === undefined) {
        return;
      }

      if (!$props.length) {
        return;
      }

      $props.each(function () {
        var $prop = $(this);
        var settings = $prop.data().settings;

        // Fixes to 24 hours actually works if you forget to change the format.
        if (settings.use24hour) {
          settings.format = settings.format.replace(/hh/, 'HH');
        }

        settings.field = $prop[0];

        new window.Pikaday(settings);
      });
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


})(window.jQuery);
