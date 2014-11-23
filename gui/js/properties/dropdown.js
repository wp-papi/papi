(function ($) {

  // Property Dropdown

  papi.properties.dropdown = {

    updateSelect: function ($this) {
      $this.parent().find('select').select2();
    }

  };

  // Events

  $(document).on('papi/property/repeater/added', '[value="dropdown"]', function (e) {
    e.preventDefault();

    papi.properties.dropdown.updateSelect($(this));
  });

})(jQuery);
