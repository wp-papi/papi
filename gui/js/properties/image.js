(function ($) {

  // Property image

  papi.properties.image = {

    /**
     * Initialize property image.
     */

    init: function () {
      $('.inside .papi-table:not(.papi-table-template) > tbody:not(:has(.papi-table-template)) .papi-property-image.gallery .attachments').sortable({
        revert: true
      });
    },

    /**
     * Add a new image.
     *
     * @param {object} $this
     */

    add: function ($this) {
      var $prop     = $this.closest('.papi-property-image'),
          $select   = $this.closest('p'),
          $target   = $prop.find('.attachments'),
          isGallery = $prop.hasClass('gallery'),
          slug      = $this.attr('data-slug');

      papi.utils.wpMediaEditor({
        multiple: isGallery
      }, function (attachment, isImage) {
        if (!isImage) {
          return;
        }

        new papi.views.Image({
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
          $image = $this.closest('.attachment');

      $prop.find('.papi-image-select').show();

      $image.remove();
    },

    /**
     * Update when added to repeater.
     *
     * @param {object} $this
     */

    update: function ($this) {
      $this.prev().find('.attachments').sortable({
        revert: true
      });
    }

  };

  // Events

  $(document).on('click', '.papi-property-image .papi-image-select > .button', function (e) {
    e.preventDefault();

    papi.properties.image.add($(this));
  });

  $(document).on('hover', '.papi-property-image .attachment', function (e) {
    e.preventDefault();

    papi.properties.image.hover($(this));
  });

  $(document).on('click', '.papi-property-image .attachment a', function (e) {
    e.preventDefault();

    papi.properties.image.remove($(this));
  });

  $(document).on('papi/property/repeater/added', '[value="image"]', function (e) {
    e.preventDefault();

    papi.properties.image.update($(this));
  });


})(jQuery);
