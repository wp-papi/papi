(function ($) {

  // Core object.
  var Core = {};

  // Initialize core.
  Core.init = function () {
    this.binds();
  };

  // Core binds.
  Core.binds = function () {

    // Make p tag clickable.
    $('.act-box-list > li > p').on('click', function () {
      window.location = $(this).prev().attr('href');
    });

    // Add our own inside class to the inside div.
    $('.act-table').closest('.inside').addClass('act-inside');

    // Add new page - search field.
    $('input[name=add-new-page-search]').on('keyup', function () {

      var $this = $(this),
          $list = $('.act-box-list'),
          val = $this.val();

      $list.find('li').each(function () {
        var $li = $(this);
        $li[$li.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
      });

    });

    // Fixing equal heights.
    var boxItems = $('.act-post-type-info'),
        boxMaxHeight = 0;

    boxItems.each(function () {
      var height = $(this).height();
      boxMaxHeight = height > boxMaxHeight ? height : boxMaxHeight;
    });

    boxItems.height(boxMaxHeight);

    // Fixing so "Add new page" get selected if it isn't.
    (function () {
      var href = typeof window.location === 'string' ? window.location : window.location.href,
          $adminmenu = $('#adminmenu');

      if (!$adminmenu.find('li.current > a.current').length) {
        href = href.substr(href.lastIndexOf('/') + 1);
        $('a[href="' + href + '"]', $adminmenu).addClass('current').parent().addClass('current');
      }
    })();

    // Simple href attribute on non links.
    $('[data-act-href]').on('click touchstart', function (e) {
      e.preventDefault();
      window.location = $(this).data('act-href');
    });

  };

  // Add the Core object to the act object.
  window.act.Core = Core;

}(window.jQuery));
