(function ($) {

  // Property image

  papi.properties.image = {

    /**
     * Add a new image.
     *
     * @param {object} $this
     */

    add: function ($this) {
      var $prop     = $this.closest('.papi-property-image'),
          $select   = $this.closest('p'),
          $target   = $prop.find('ul'),
          isGallery = $prop.hasClass('gallery'),
          slug      = $this.attr('data-slug');

      papi.utils.wpMediaEditor({
        multiple: isGallery
      }, function (attachment, isImage) {
        if (!isImage) {
          return;
        }

        new papi.view.Image({
          el: $target
        }).render({
          image: attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.url : attachment.url,
          id: attachment.id,
          slug: slug
        });

        if (!isGallery) {
          $select.hide();
        }

      });
    },

    /**
     * Toggle the remove button.
     *
     * @param {object} $this
     */

    hover: function ($this) {
      $this.find('a').toggle();
    },

    /**
     * Remove a image.
     *
     * @param {object} $this
     */

    remove: function ($this) {
      var $prop  = $this.closest('.papi-property-image'),
          $image = $this.closest('li');

      $prop.find('.papi-image-select').show();

      $image.remove();
    }

  };

  // Events

  $(document).on('click', '.papi-property-image .papi-image-select > .button', function (e) {
    e.preventDefault();

    papi.properties.image.add($(this));
  });

  $(document).on('hover', '.papi-property-image ul li', function (e) {
    e.preventDefault();

    papi.properties.image.hover($(this));
  });

  $(document).on('click', '.papi-property-image ul li a', function (e) {
    e.preventDefault();

    papi.properties.image.remove($(this));
  });

})(jQuery);
