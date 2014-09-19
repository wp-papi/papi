(function ($) {

  // Add image
  $('body').on('click', '.act-property-image .act-image-select > button', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $prop   = $this.closest('.act-property-image')
        $select = $this.closest('p')
        $target = $prop.find('ul'),
        gallery = $prop.hasClass('gallery')
        options = $this.data('act-options');

    // Open the WordPress media editor
    act.Utils.wpMediaEditor(function (attachment, isImage) {
      if (!isImage) {
        return;
      }

      new act.view.Image({
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
  $('body').on('hover', '.act-property-image ul li', function (e) {
    e.preventDefault();
    $(this).find('a').toggle();
  });

  // Remove image
  $('body').on('click', '.act-property-image ul li a', function (e) {
    e.preventDefault();

    var $this = $(this),
        $prop = $this.closest('.act-property-image')
        $li   = $this.closest('li');

    $prop
      .find('.act-image-select')
      .show();

    $li
      .remove();
  });

})(jQuery);
