import $ from 'jquery';

class Core {

  /**
   * Initialize Papi core class.
   */
  static init() {
    const core = new Core();

    core.binds();
    core.setEqualBoxHeights();
    core.setSelectedMenuItem();
    core.addCurrentClassToMenuItem();
  }

  /**
   * Autosave fields on heartbeat.
   *
   * @param {object} e
   * @param {object} xhr
   * @param {object} options
   */
  autosave(e, xhr, options) {
    const fields = jQuery('[name*="papi_"]');
    const data = {};
    const reg = /action\=(.+?)\&/;
    const val = reg.exec(options.data);

    if (val.length && val[1] === 'heartbeat') {
        fields.each(function () {
          const $this = $(this);

          data[$this.attr('name')] = $this.val();
      });

      options.data += '&' + jQuery.param(data);
    }
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $('.papi-box-list > li > p').on('click', this.redirect);
    $('input[name="add-new-page-search"]').on('keyup', this.search);
    $('[data-papi-href]').on('click touchstart', this.redirect);
    $(window).resize(this.setEqualBoxHeights);

    if ('select2' in $.fn) {
      $('.inside .papi-table tr .papi-component-select2').select2();
    }

    $('.papi-meta-type-term button.handlediv').on('click', this.handlediv);

    // Autosave fields on heartbeat.
    $(document).ajaxSend(this.autosave);
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
   * Handle expanded postbox div.
   *
   * @param  {object} e
   */
  handlediv(e) {
    e.preventDefault();

    const $this   = $(this);
    const $parent = $this.parent();
    const $inside = $parent.find('.inside');

    $parent.parent().toggleClass('closed');
    $inside.toggleClass('papi-hide');
    $this.attr('aria-expanded', !$inside.hasClass('papi-hide'));
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

    $list.find('.papi-box-item').each(function() {
      let $item = $(this);
      $item[$item.text().toLowerCase().indexOf(val) === -1 ? 'addClass' : 'removeClass']('papi-hide');
    });
  }

  /**
   * Set equal height on page type boxes.
   */
  setEqualBoxHeights() {
    let $boxItems  = $('.papi-box-item');
    let thumbnails = $boxItems.find('.papi-post-type-screenshot').length > 0;
    let boxMaxHeight = 0;

    if (!thumbnails) {
      $boxItems = $('.papi-post-type-info');
    }

    $boxItems.each(function () {
      let $this  = $(this);

      if (thumbnails) {
        $this.find('.papi-post-type-info').removeAttr('style');
      } else {
        $this.removeAttr('style');
      }

      let height = $this.height();
      boxMaxHeight = height > boxMaxHeight ? height : boxMaxHeight;
    });

    $boxItems.each(function () {
      let $this  = $(this);
      let height = boxMaxHeight;

      if (thumbnails) {
        let $thumb = $this.find('.papi-post-type-screenshot');

        if ($thumb.length) {
          height = height - $thumb.height();
        } else {
          height += 5;
        }

        $this.find('.papi-post-type-info').height(height);
      } else {
        $this.height(height);
      }
    });
  }

  /**
   * Set selected menu item if it isn't selected.
   */
  setSelectedMenuItem() {
    let href = typeof window.location === 'string' ? window.location : window.location.href;
    let $adminmenu = $('#adminmenu');

    if (!$adminmenu.find('li.current > a.current').length) {
      href = href.substr(href.lastIndexOf('/') + 1);
      href = href.replace(/\%2F/g, '/');
      $('a[href="' + href + '"]', $adminmenu).addClass('current').parent().addClass('current');
    }
  }
}

export default Core;
