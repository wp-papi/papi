!function (window, $) {

  // Tabs object.
  var Tabs = {};

  // Initialize Tabs.
  Tabs.init = function () {
    this.binds();
  };

  // Tabs binds.
  Tabs.binds = function () {

    // Tabs (will be rewritten)
    $('a[data-ptb-tab]').on('click', function (e) {
      e.preventDefault();

      var $this = $(this)
        , tab = $this.data('ptb-tab');

      $('a[data-ptb-tab]').parent().removeClass('active');
      $this.parent().addClass('active');

      $('div[data-ptb-tab]').removeClass('active').hide();
      $('div[data-ptb-tab=' + tab + ']').addClass('active').show();
    });

  };

  // Add the Tabs object to the Ptb object.
  window.Ptb.Tabs = Tabs;

}(window, window.jQuery);