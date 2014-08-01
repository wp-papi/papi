(function ($) {

  // Add image
  $('body').on('click', '.ptb-property-image .ptb-image-select > button', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $prop   = $this.closest('.ptb-property-image')
        $select = $this.closest('p')
        $target = $prop.find('ul'),
        gallery = $prop.hasClass('gallery')
        options = $this.data('ptb-options');

    // Open the WordPress media editor
    ptb.Utils.wpMediaEditor(function (attachment, isImage) {
      if (!isImage) {
        return;
      }

      new ptb.view.Image({
        el: $target
      }).render({
        image: attachment.url,
        id: attachment.id,
        slug: options.slug
      });

      if (!gallery) {
        $select
          .hide();
      }
    });

  });

  // Toggle remove x
  $('body').on('hover', '.ptb-property-image ul li', function (e) {
    e.preventDefault();
    $(this).find('a').toggle();
  });

  // Remove image
  $('body').on('click', '.ptb-property-image ul li a', function (e) {
    e.preventDefault();

    var $this = $(this),
        $prop = $this.closest('.ptb-property-image')
        $li   = $this.closest('li');

    $prop
      .find('.ptb-image-select')
      .show();

    $li
      .remove();
  });

})(jQuery);