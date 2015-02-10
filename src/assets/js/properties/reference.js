(function ($) {

  'use strict';

  // Property reference

  papi.properties.reference = {

    /**
     * Toggle page type div.
     */

    toggle: function ($this) {
      $this
        .parent()
        .toggleClass('closed')
        .next()
        .toggle();
    }
  };

  // Events

  $(document).on('click', '.papi-property-reference .handlediv', function (e) {
    e.preventDefault();

    papi.properties.reference.toggle($(this));
  });

})(window.jQuery);
