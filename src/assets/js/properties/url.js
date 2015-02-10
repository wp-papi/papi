(function ($) {

  'use strict';

  // Property url

  papi.properties.url = {

    /**
     * Add new media file.
     *
     * @param {object} $this
     */

    add: function ($this) {
      papi.utils.wpMediaEditor().on('insert', function (attachment, isImage) {
        $this.prev().val(attachment.url);
      }).open();
    }

  };

  // Events

  $(document).on('click', '.papi-url-media-button', function (e) {
    e.preventDefault();

    papi.properties.url.add($(this));
  });

})(window.jQuery);
