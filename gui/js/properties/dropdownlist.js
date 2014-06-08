!function ($) {

  // Select2 is used for PropertyDropdownList
  if ('select2' in $.fn) {
    $('select[data-ptb-property="dropdown"]').select2();
  }

}(window.jQuery);