import $ from 'jquery';

class Core {

  /**
   * Initialize Papi core class.
   */

  static init() {
    let core = new Core();

    core.binds();
    core.setEqualBoxHeights();
    core.setSelectedMenuItem();
    core.addCurrentClassToMenuItem();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $('.papi-box-list > li > p').on('click', this.redirect);
    $('input[name="add-new-page-search"]').on('keyup', this.search);
    $('[data-papi-href]').on('click touchstart', this.redirect);

    if ('select2' in $.fn) {
      $('.inside .papi-table tr .papi-vendor-select2').select2();
    }
  }

  /**
   * Add current class to menu item.
   */

  addCurrentClassToMenuItem() {
    let $submenu = $('.wp-has-current-submenu .wp-submenu');
    let $menuitem = $submenu.find('a[href*="papi-add-new-page"]').parent();

    if (!$menuitem.hasClass('current') && !$submenu.find('li.current').length) {
      $menuitem.addClass('current');
    }
  }

  /**
   * Redirect to location from `papi-href` data attribute
   * or closest tag with href attribute.
   *
   * @param {object} e
   */

  redirect(e) {
    e.preventDefault();
    let $this    = $(this);
    let papiHref = $this.data().papiHref;

    if (papiHref !== undefined) {
      window.location = papiHref;
    } else {
      window.location = $(this).closest('[href]').attr('href');
    }
  }

  /**
   * Search in page types box list.
   *
   * @param {object} e
   */

  search(e) {
    e.preventDefault();

    let $this = $(this);
    let $list = $('.papi-box-list');
    let val   = $this.val();

    $list.find('.papi-box-item').each(() => {
      let $item = $(this);
      $item[$item.text().toLowerCase().indexOf(val) === -1 ? 'addClass' : 'removeClass']('.papi-hide');
    });
  }

  /**
   * Set equal height on page type boxes.
   */

  setEqualBoxHeights() {
    let boxItems = $('.papi-post-type-info');
    let boxMaxHeight = 0;

    boxItems.each(function () {
      let height = $(this).height();
      boxMaxHeight = height > boxMaxHeight ? height : boxMaxHeight;
    });

    boxItems.height(boxMaxHeight);
  }

  /**
   * Set selected menu item if it isn't selected.
   */

  setSelectedMenuItem() {
    let href = typeof window.location === 'string' ? window.location : window.location.href;
    let $adminmenu = $('#adminmenu');

    if (!$adminmenu.find('li.current > a.current').length) {
      href = href.substr(href.lastIndexOf('/') + 1);
      $('a[href="' + href + '"]', $adminmenu).addClass('current').parent().addClass('current');
    }
  }

}

export default Core;
