(function ($) {

  // Property Post

  papi.properties.post = {

    updateSelect: function ($this) {
      $this.parent().find('select').select2();
    }

  };

  // Events

  $(document).on('papi/property/repeater/added', '[value="PropertyPost"]', function (e) {
    e.preventDefault();

    papi.properties.post.updateSelect($(this));
  });

})(jQuery);
