!function (window, $) {

  // Core object.
  var Core = {};

  // Initialize core.
  Core.init = function () {
    this.binds();
  };

  // Core binds.
  Core.binds = function () {

    // Make p tag clickable.
    $('.ptb-box-list > li > p').on('click', function () {
      window.location = $(this).prev().attr('href');
    });

    // Add new page - search field.
    $('input[name=add-new-page-search]').on('keyup', function () {

      var $this = $(this)
        , $list = $('.ptb-box-list')
        , val = $this.val();

      $list.find('li').each(function () {
        var $li = $(this);
        $li[$li.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
      });

    });

    // Fixing so "Add new page" get selected if it isn't.
    (function () {
      var href = typeof window.location === 'string' ? window.location : window.location.href
        , $adminmenu = $('#adminmenu');

      if (!$adminmenu.find('li.current > a.current').length) {
        href = href.substr(href.lastIndexOf('/') + 1);
        $('a[href="' + href + '"]', $adminmenu).addClass('current').parent().addClass('current');
      }
		})();

  };

  // Add the Core object to the ptb object.
  window.Ptb.Core = Core;

}(window, window.jQuery);