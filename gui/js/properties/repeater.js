(function ($) {

  /**
   * Update table row numbers.
   *
   * @param {object} $tbody
   */

  function updateRowNumber ($tbody) {
    $tbody
    .find('tr')
    .each(function (index) {
      var $this = $(this);

      $this
        .find('td:first-child span')
        .text(index + 1);

      $this
        .find('input,select,textarea')
        .each(function () {
          $(this).attr('name', $(this).attr('name').replace(/(\[\d+\])/, '[' + index + ']'));
        });
    });
  }

  // Add new item and update the array index in html name.
  $('.papi-property-repeater').on('click', '.bottom a.button', function (e) {
    e.preventDefault();

    var $repeater = $(this).closest('.papi-property-repeater'),
        $template = $('.repeater-template tr', $repeater).clone(),
        $table = $repeater.find('> .papi-table tbody'),
        counter = $table.children().length,
        html = $template.html(),
        dataNameRegex = /data\-name\=/g,
        attrNameRegex = /name\=\"\papi\_\w+(\[\d+\])\[(\w+)\]\"/g,
        attrNameValue = '[' + counter + ']';

    html = html.replace(dataNameRegex, 'name=');

    // Update array number in html name and name if ends with a number.
    html = html.replace(attrNameRegex, function (match, num) {
      return match.replace(num, attrNameValue);
    });

    $template
      .html(html)
      .find('td:first-child span')
      .text(counter + 1);

    $template
      .appendTo($table);

    $('html, body')
      .animate({
        scrollTop: $('> tr:last', $table).offset().top
      });
  });

  // Remove item
  $('.papi-property-repeater').on('click', '.repeater-remove-item', function (e) {
    e.preventDefault();

    var $this = $(this),
        $tbody = $this.closest('.papi-property-repeater tbody');

    $this
      .closest('tr')
      .remove();

    updateRowNumber($tbody);
  });

  // Add support for sortable list.
  $('.papi-property-repeater tbody').sortable({
    revert: true,
    helper: function (e, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    },
    stop: function () {
      updateRowNumber($(this).closest('.papi-property-repeater tbody'));
    }
  });

})(jQuery);
