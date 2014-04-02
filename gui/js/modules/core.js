!function (window, $) {

  // Core object.
  var Core = {};

  // Initialize core.
  Core.init = function () {
    this.binds();
  };

  // Core binds.
  Core.binds = function () {

    // Add new page search field.
    $('input[name=add-new-page-search]').on('keyup', function () {

      var $this = $(this)
        , $list = $('.ptb-box-list')
        , val = $this.val();

      $list.find('li').each(function () {
        var $li = $(this);
        $li[$li.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
      });

    });

  };

  // Add the Core object to the ptb object.
  window.ptb.Core = Core;

}(window, window.jQuery);