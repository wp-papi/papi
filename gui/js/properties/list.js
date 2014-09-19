(function ($) {

  // Property list binds

  // Replace all template name attributes with data-name attribute.
  $('ul.papi-property-list-template > li [name*=papi_]').each(function () {
    var $this = $(this);

    $this
      .attr('data-name', $this
        .attr('name'));

    $this
      .removeAttr('name');
  });

  // Add new item and update the array index in html name.
  $('.papi-property-list').on('click', '.papi-property-list-add-new-item', function (e) {
    e.preventDefault();

    var $list = $(this).closest('.papi-property-list'),
        $template = $('.papi-property-list-template > li', $list).clone(),
        $items = $('.papi-property-list-items', $list),
        counter = $items.children().length,
        html = $template.html(),
        dataNameRegex = /data\-name\=/g,
        attrNameRegex = /name\=\"\act_\w+(\[\d+\])\[(\w+)\]\"/g,
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
  $('.papi-property-list').on('click', '.papi-property-list-remove-item', function (e) {
    e.preventDefault();

    $(this)
      .closest('li')
      .remove();
  });

  // Add support for sortable list.
  $('.papi-property-list-items').sortable({
    revert: true,
    stop: function () {
      $(this).closest('.papi-property-list-items').find('li').each(function (index) {
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
