(function ($) {

  'use strict';

  // Select2
  $('.inside .papi-table:not(.papi-table-template) tr .papi-vendor-select2')
    .filter(function () {
      return !$(this).closest('table').hasClass('papi-table-template');
    })
    .select2();

})(window.jQuery);
