import $ from 'jquery';
import select2Options from 'components/select2';

class Core {

  /**
   * Initialize Papi core class.
   */
  static init() {
    const core = new Core();

    core.addCurrentClassToMenuItem();
    core.binds();
    core.pageTypeSwitcher();
    core.prepareBoxes();
    core.setSelectedMenuItem();
  }

  /**
   * Autosave fields on heartbeat.
   *
   * @param {object} e
   * @param {object} xhr
   * @param {object} options
   */
  autosave(e, xhr, options) {
    const fields = $('[name*="papi_"]');
    const data = {};
    const reg = /action=(.+?)&/;
    const val = reg.exec(options.data);
    const id  = /post_id=\d+/.exec(options.data);

    if (val !== null && val.length && val[1] === 'heartbeat' && id != null && id.length) {
      fields.each(function () {
        const $this = $(this);

        data[$this.attr('name')] = $this.val();
      });

      options.data += '&' + $.param(data);
    }
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $('.papi-box-list > li > p').on('click', this.redirect);
    $('input[name="add-new-page-search"]').on('keyup', this.search);
    $('[data-papi-href]').on('click touchstart', this.redirect);

    if ('select2' in $.fn) {
      $('.inside .papi-table tr .papi-component-select2').select2(select2Options);

      // Fix issue with browsers where selected attribute is not removed correct.
      $(document.body).on('change', 'select.papi-component-select2', function () {
        $(this).find('option[selected]').removeAttr('selected');
      });
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
   * Page type switcher.
   */
  pageTypeSwitcher() {
    $('.misc-pub-section.curtime.misc-pub-section-last').removeClass('misc-pub-section-last');
    $('#papi-page-type-switcher-edit').on('click', function(e) {
      e.preventDefault();
      $(this).hide();
      $('.papi-page-type-switcher > div').slideDown();
    });
    $('#papi-page-type-switcher-save').on('click', function(e) {
      e.preventDefault();
      $('.papi-page-type-switcher > div').slideUp();
      $('#papi-page-type-switcher-edit').show();
      $('.papi-page-type-switcher > span').text($('.papi-page-type-switcher select :selected').text());
    });
    $('#papi-page-type-switcher-cancel').on('click', function(e) {
      e.preventDefault();
      $('.papi-page-type-switcher > div').slideUp();
      $('#papi-page-type-switcher-edit').show();
    });
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

    // Destroy masonry before searching the list.
    $('.papi-box-list').masonry('destroy');

    $list.find('.papi-box-item').each(function() {
      let $item = $(this);

      if ($item.text().toLowerCase().indexOf(val) === -1) {
        $item.addClass('papi-hide');
      } else {
        $item.removeClass('papi-hide');
      }
    });

    // Enable masonry after searching the list.
    $('.papi-box-list').masonry({
      itemSelector: '.papi-box-item:not(.papi-hide)',
      isResizable: true
    });
  }

  /**
   * Prepare boxes with equal height.
   */
  prepareBoxes() {
    let $boxItems = $('.papi-box-item');
    let thumbnails = $boxItems.find('.papi-page-type-screenshot').length;
    let boxMaxHeight = 0;

    if (!thumbnails) {
      $boxItems = $('.papi-page-type-info');
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
        let $thumb = $this.find('.papi-page-type-screenshot');

        if ($thumb.length) {
          height = height - $thumb.height();
        } else {
          height += 5;
        }

        $this.find('.papi-page-type-info').height(height);
      } else {
        $this.height(height);
      }
    });

    // Enable masonry after setting equal height.
    $('.papi-box-list').masonry({
      itemSelector: '.papi-box-item:not(.papi-hide)',
      isResizable: true
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
      href = href.replace(/%2F/g, '/');
      $('a[href="' + href + '"]', $adminmenu).addClass('current').parent().addClass('current');
    }
  }
}

export default Core;
