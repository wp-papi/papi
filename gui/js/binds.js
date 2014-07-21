(function ($) {

  // Use WordPress media uploader to set files on property url
  // when `data-ptb-action` has the action `mediauploader`
  $('body').on('click', '[data-ptb-action="mediauploader"]', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $target = $this.prev();

    ptb.Utils.wp_media_editor($this, $target);
  });

  // Use Select2 for property dropdown list.
  if ('select2' in $.fn) {
    $('select[data-ptb-property="dropdown"]').select2();
  }

  // Use Pikaday for property date.
  if ('pikaday' in $.fn) {
    $('input[data-ptb-property="date"]').pikaday({
      format: 'YYYY-MM-DD',
      setDefaultDate: true
    });
  }

  // Property image binds.
  $('div[data-ptb-property="image"] .ptb-image-select > button').on('click', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $target = $this.closest('div'),
        options = $this.data('ptb-options');

    // Open the WordPress media editor
    ptb.Utils.wpMediaEditor(function (attachment, isImage) {
      if (!isImage) {
        return;
      }

      new ptb.view.Image({
        el: $target.empty()
      }).render({
        image: attachment.url,
        id: attachment.id,
        slug: options.slug
      })
    });

  });

  $('div[data-ptb-property="image"]').on('hover', function (e) {
    e.preventDefault();
    $(this).find('a').toggle();
  });

  $('div[data-ptb-property="image"] a.ptb-image-remove').on('click', function (e) {
    e.preventDefault();

    var $this = $(this).closest('div[data-ptb-property="image"]');

    $this
      .find('.ptb-image-select')
      .show();

    $this
      .find('input, img, a')
      .remove();
  });

})(jQuery);