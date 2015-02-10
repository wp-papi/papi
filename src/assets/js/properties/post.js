(function ($) {
  'use strict';

  // Property Post

  papi.properties.post = {

    /**
     * Initialize pikaday field when added to repeater.
     *
     * @param object $this
     */

    updateSelect: function ($this) {
      $this.parent().find('select').select2();
    }

  };

  // Events

  $(document).on('papi/property/repeater/added', '[value="post"]', function (e) {
    e.preventDefault();

    papi.properties.post.updateSelect($(this));
  });

})(window.jQuery);
