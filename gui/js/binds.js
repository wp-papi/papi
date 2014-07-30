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

  // Property list binds

  // Replace all template name attributes with data-name attribute.
  $('ul.ptb-property-list-template > li [name*=ptb_]').each(function () {
    var $this = $(this);

    $this.attr('data-name', $this.attr('name'));
    $this.removeAttr('name');
  });

  // Add new item and update the array index in html name.
  $('.ptb-property-list').on('click', '.ptb-property-list-add-new-item', function (e) {
    e.preventDefault();

    var $list = $(this).closest('.ptb-property-list'),
        $template = $('.ptb-property-list-template > li', $list).clone(),
        $items = $('.ptb-property-list-items', $list),
        counter = $items.children().length,
        html = $template.html(),
        dataNameRegex = /data\-name\=/g,
        attrNameRegex = /name\=\"\ptb_\w+(\[\d+\])\[(\w+)\]\"/g,
        attrNameValue = '[' + counter + ']';

    html = html.replace(dataNameRegex, 'name=');

    // Update array number in html name and name if ends with a number.
    html = html.replace(attrNameRegex, function (match, num) {
      return match.replace(num, attrNameValue);
    });

    $template
      .html(html)
      .find('tr.num span')
      .text(counter + 1);

    $template
      .appendTo($items);

    $('html, body')
      .animate({
        scrollTop: $('li:last', $items).offset().top
      });
  });

  // Remove item
  $('.ptb-property-list').on('click', '.ptb-property-list-remove-item', function (e) {
    e.preventDefault();

    $(this).closest('li').remove();
  });

})(jQuery);