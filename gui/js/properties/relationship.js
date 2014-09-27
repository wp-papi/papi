(function ($) {

  // Add page reference to list
  $('.papi-property-relationship .relationship-left').on('click', 'li', function (e) {
    e.preventDefault();

    var $this = $(this),
        $li = $this.clone(),
        $list = $this.closest('.papi-property-relationship').find('.relationship-right ul');

    $li.find('span.icon').removeClass('plus').addClass('minus');
    $li.find('input').attr('name', $li.find('input').data('name'));
    $li.appendTo($list);
  });

  // Remove page reference from list
  $('.papi-property-relationship .relationship-right').on('click', 'li', function (e) {
    e.preventDefault();
    $(this).remove();
  });

  // Search field
  $('.papi-property-relationship .relationship-left .relationship-search input[type=search]').on('keyup', function () {

    var $this = $(this),
        $list = $this.closest('.papi-property-relationship').find('.relationship-left ul'),
        val = $this.val();

    $list.find('li').each(function () {
      var $li = $(this);
      $li[$li.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
    });

  });

})(jQuery);