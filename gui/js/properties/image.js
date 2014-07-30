(function ($) {

  // Add image
  $('body').on('click', '.ptb-property-image .ptb-image-select > button', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $select = $this.closest('p')
        $target = $this.closest('.ptb-property-image').find('.ptb-image-area'),
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
      });

      $select
        .hide();
    });

  });

  // Toggle remove x
  $('body').on('hover', '.ptb-property-image', function (e) {
    e.preventDefault();
    $(this).find('a').toggle();
  });

  // Remove image
  $('body').on('click', '.ptb-property-image .ptb-image-area a', function (e) {
    e.preventDefault();

    var $this = $(this).closest('.ptb-property-image');

    $this
      .find('.ptb-image-select')
      .show();

    $this
      .find('input, img, a')
      .remove();
  });

})(jQuery);