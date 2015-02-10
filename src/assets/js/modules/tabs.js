(function ($) {

  'use strict';

  // Tabs object.
  var tabs = {};

  // Initialize Tabs.
  tabs.init = function () {
    this.binds();
  };

  // Tabs binds.
  tabs.binds = function () {

    // Tabs (will be rewritten)
    $('a[data-papi-tab]').on('click', function (e) {
      e.preventDefault();

      var $this = $(this);
      var tab = $this.data('papi-tab');

      $('a[data-papi-tab]').parent().removeClass('active');
      $this.parent().addClass('active');

      $('div[data-papi-tab]').removeClass('active').hide();
      $('div[data-papi-tab=' + tab + ']').addClass('active').show();
    });

  };

  // Add utils to the papi object.
  window.papi.tabs = tabs;

}(window.jQuery));
