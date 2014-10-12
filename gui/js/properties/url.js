(function ($) {

  // Property url

  papi.properties.url = {

    /**
     * Add new media file.
     *
     * @param {object} $this
     */

    add: function ($this) {
      papi.utils.wpMediaEditor({}, $this.prev());
    }

  };

  // Events

  $(document).on('click', '.papi-url-media-button', function (e) {
    e.preventDefault();

    papi.properties.url.add($(this));
  });

})(jQuery);