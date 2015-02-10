(function ($) {

  'use strict';

  // Property Dropdown

  papi.properties.dropdown = {

    /**
     * Update select if isn't a select2.
     */

    updateSelect: function ($this) {
      var $select = $this.parent().find('select');

      if ($select.hasClass('papi-vendor-select2')) {
        $select.select2();
      }
    }

  };

  // Events

  $(document).on('papi/property/repeater/added', '[value="dropdown"]', function (e) {
    e.preventDefault();

    papi.properties.dropdown.updateSelect($(this));
  });

})(window.jQuery);
