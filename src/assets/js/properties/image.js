(function ($) {

  'use strict';

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
      var $prop     = $this.closest('.papi-property-image');
      var $select   = $this.closest('p');
      var $target   = $prop.find('.attachments');
      var multiple  = $prop.hasClass('gallery');
      var slug      = $this.attr('data-slug');

      papi.utils.wpMediaEditor({
        library: {
          type: 'image'
        },
        multiple: multiple
      }).on('insert', function (attachment, isImage) {
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

        if (!multiple) {
          $select.hide();
        }

      }).open();
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
      var $prop  = $this.closest('.papi-property-image');
      var $image = $this.closest('.attachment');

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
    },

    /**
     * Replace image with another one.
     *
     * @param {object} $this
     */

    replace: function ($this) {
      var $img = $this.find('img[src]');
      var $input = $this.find('input[type=hidden]');
      var postId = $input.val();

      papi.utils.wpMediaEditor({
        library: {
          type: 'image'
        },
        multiple: false
      }).on('open', function () {
        var selection = papi.utils.wpMediaFrame.state().get('selection');
        var attachment = window.wp.media.attachment(postId);

        attachment.fetch();
        selection.add(attachment ? [attachment] : []);
      }).on('insert', function (attachment, isImage) {
        $img.attr('src', attachment.sizes.thumbnail !== undefined ? attachment.sizes.thumbnail.url : attachment.url);
        $input.val(attachment.id);
      }).open();
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
    e.stopPropagation();

    papi.properties.image.remove($(this));
  });

  $(document).on('papi/property/repeater/added', '[value="image"]', function (e) {
    e.preventDefault();

    papi.properties.image.update($(this));
  });

  $(document).on('click', '.papi-property-image .attachment', function (e) {
    e.preventDefault();

    papi.properties.image.replace($(this));
  });

})(window.jQuery);
