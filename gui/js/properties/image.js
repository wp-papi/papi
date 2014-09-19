(function ($) {

  // Add image
  $('body').on('click', '.papi-property-image .papi-image-select > button', function (e) {
    e.preventDefault();

    var $this   = $(this),
        $prop   = $this.closest('.papi-property-image')
        $select = $this.closest('p')
        $target = $prop.find('ul'),
        gallery = $prop.hasClass('gallery')
        options = $this.data('papi-options');

    // Open the WordPress media editor
    act.Utils.wpMediaEditor(function (attachment, isImage) {
      if (!isImage) {
        return;
      }

      new papi.view.Image({
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
  $('body').on('hover', '.papi-property-image ul li', function (e) {
    e.preventDefault();
    $(this).find('a').toggle();
  });

  // Remove image
  $('body').on('click', '.papi-property-image ul li a', function (e) {
    e.preventDefault();

    var $this = $(this),
        $prop = $this.closest('.papi-property-image')
        $li   = $this.closest('li');

    $prop
      .find('.papi-image-select')
      .show();

    $li
      .remove();
  });

})(jQuery);
