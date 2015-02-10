(function ($) {

  'use strict';

  // Core object.
  var core = {};

  // Initialize core.
  core.init = function () {
    this.binds();
  };

  // Core binds.
  core.binds = function () {

    // Make p tag clickable.
    $('.papi-box-list > li > p').on('click', function () {
      window.location = $(this).prev().attr('href');
    });

    // Add new page - search field.
    $('input[name=add-new-page-search]').on('keyup', function () {

      var $this = $(this);
      var $list = $('.papi-box-list');
      var val = $this.val();

      $list.find('.papi-box-item').each(function () {
        var $item = $(this);
        $item[$item.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
      });

    });

    // Fixing equal heights.
    var boxItems = $('.papi-post-type-info');
    var boxMaxHeight = 0;

    boxItems.each(function () {
      var height = $(this).height();
      boxMaxHeight = height > boxMaxHeight ? height : boxMaxHeight;
    });

    boxItems.height(boxMaxHeight);

    // Fixing so "Add new page" get selected if it isn't.
    (function () {
      var href = typeof window.location === 'string' ? window.location : window.location.href;
      var $adminmenu = $('#adminmenu');

      if (!$adminmenu.find('li.current > a.current').length) {
        href = href.substr(href.lastIndexOf('/') + 1);
        $('a[href="' + href + '"]', $adminmenu).addClass('current').parent().addClass('current');
      }
    })();

    // Simple href attribute on non links.
    $('[data-papi-href]').on('click touchstart', function (e) {
      e.preventDefault();
      window.location = $(this).data('papi-href');
    });

    // Add current class to add new menu link.
    var $submenu = $('.wp-has-current-submenu .wp-submenu');
    var $menuitem = $submenu.find('a[href*="papi-add-new-page"]').parent();

    if (!$menuitem.hasClass('current') && !$submenu.find('li.current').length) {
      $menuitem.addClass('current');
    }

  };

  // Add the core object to the papi object.
  window.papi.core = core;

}(window.jQuery));
