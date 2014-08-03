(function ($) {

  // Property list binds

  // Replace all template name attributes with data-name attribute.
  $('ul.ptb-property-list-template > li [name*=ptb_]').each(function () {
    var $this = $(this);

    $this
      .attr('data-name', $this
        .attr('name'));

    $this
      .removeAttr('name');
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
        scrollTop: $('> li:last', $items).offset().top - 40
      });
  });

  // Remove item
  $('.ptb-property-list').on('click', '.ptb-property-list-remove-item', function (e) {
    e.preventDefault();

    $(this)
      .closest('li')
      .remove();
  });

  // Add support for sortable list.
  $('.ptb-property-list-items').sortable({
    revert: true,
    stop: function () {
      $(this).closest('.ptb-property-list-items').find('li').each(function (index) {
        var $this = $(this);

        $this
          .find('tr.num span')
          .text(index + 1);

        $this
          .find(':input')
          .each(function () {
            $(this).attr('name', $(this).attr('name').replace(/(\[\d+\])/, '[' + index + ']'));
          });
      });
    }
  });

})(jQuery);