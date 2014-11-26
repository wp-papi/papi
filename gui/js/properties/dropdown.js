(function ($) {

  // Property Dropdown

  papi.properties.dropdown = {

    updateSelect: function ($this) {
      var $select = $this.parent().find('select')

      if ($select.hasClass('papi-vendor-select2')) {
        $select.select2();
      }
    }

  };

  // Events

  $(document).on('papi_property_repeater_added', '[value="dropdown"]', function (e) {
    e.preventDefault();

    papi.properties.dropdown.updateSelect($(this));
  });

})(jQuery);
