(function ($) {

  // Tabs object.
  var Tabs = {};

  // Initialize Tabs.
  Tabs.init = function () {
    this.binds();
  };

  // Tabs binds.
  Tabs.binds = function () {

    // Tabs (will be rewritten)
    $('a[data-act-tab]').on('click', function (e) {
      e.preventDefault();

      var $this = $(this),
          tab = $this.data('act-tab');

      $('a[data-act-tab]').parent().removeClass('active');
      $this.parent().addClass('active');

      $('div[data-act-tab]').removeClass('active').hide();
      $('div[data-act-tab=' + tab + ']').addClass('active').show();
    });

  };

  // Add utils to the act object.
  window.act.Tabs = Tabs;

}(jQuery));
