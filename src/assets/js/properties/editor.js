import $ from 'jquery';

/* global tinymce, tinyMCE, tinyMCEPreInit, QTags */

class Editor {

  /**
   * Custom TinyMCE settings.
   *
   * @var {object}
   */
  get customTinyMCESettings() {
    return {
      elements: [],
      mode: 'exact',
      /* eslint-disable */
      theme_advanced_resizing: true
      /* eslint-enable */
    };
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $(document).on('papi/property/repeater/added', '[data-property="editor"]', this.update.bind(this));
    $(this.loaded);
  }

  /**
   * Get TinyMCE editor.
   *
   * @param {string} id
   *
   * @return {object}
   */
  createTinyMceEditor(id) {
    let mceInit;

    if (!tinyMCEPreInit.mceInit[id]) {
      const obj = tinyMCEPreInit.mceInit.content === undefined
        ? tinyMCEPreInit.mceInit.papiHiddenEditor : tinyMCEPreInit.mceInit.content;
      mceInit = tinyMCEPreInit.mceInit[id] = $.extend({}, obj);
    } else {
      mceInit = tinyMCEPreInit.mceInit[id];
    }

    mceInit = $.extend(mceInit, this.customTinyMCESettings, {
      selector: '#' + id,
      elements: id
    });

    tinymce.init(mceInit);
  }

  /**
   * Close all QTags.
   *
   * @param {object} $iframe
   */
  closeAllQTags($iframe) {
    if (typeof QTags === 'undefined' || typeof $iframe.canvas === 'undefined') {
      return;
    }

    QTags.closeAllTags($iframe.id);
  }

  /**
   * Get elements.
   *
   * @param {object} selectors
   *
   * @return {object}
   */
  getElements(selectors) {
    return {
      $dom: tinyMCE.DOM,
      $iframe: $('#' + selectors.iframe)
    };
  }

  /**
   * Get TinyMCE Id.
   *
   * @param {string} id
   *
   * @return {string}
   */
  getId(id) {
    return id.replace('wp-', '').replace('-wrap', '');
  }

  /**
   * Get selectors.
   *
   * @param {string} id
   *
   * @return {object}
   */
  getSelectors(id) {
    return {
      id: id,
      iframe: id + '_ifr',
      htmlTab: '#' + id + '-html',
      visualTab: '#' + id + '-tmce',
      wrap: 'wp-' + id + '-wrap'
    };
  }

  /**
   * Fix so visual tab is visible at page load.
   */
  static init() {
    new Editor().binds();
  }

  /**
   * Change post id if zero when dom is loaded.
   */
  loaded() {
    if (window.wp.media.view.settings.post.id === 0) {
      window.wp.media.view.settings.post.id = parseInt($('#post_ID').val(), 10);
    }
  }

  /**
   * Initialize QTags.
   *
   * @param {string} id
   */
  qtInit(id) {
    const qtContent = tinyMCEPreInit.qtInit.content === undefined
      ? tinyMCEPreInit.qtInit.papiHiddenEditor : tinyMCEPreInit.qtInit.content;

    if (qtContent !== undefined) {
      const qtInit = tinyMCEPreInit.qtInit[id] = $.extend({}, qtContent, {
        id: id,
        buttons: qtContent.buttons.replace(',fullscreen', '')
      });

      try {
        /* eslint-disable */
        new QTags(qtInit);
        /* eslint-enable */
      } catch (e) {
      }

      QTags._buttonsInit();
    }
  }

  /**
   * Update editor when it is added to repeater.
   *
   * @param {object} e
   */
  update(e) {
    e.preventDefault();

    const $this   = $(e.currentTarget);
    const $prev   = $this.prev();
    let   id      = '';

    // Support `vertical` layout in flexible and repeater.
    if ($prev.length && $prev[0].tagName.toLowerCase() === 'tr') {
      id = this.getId($prev.find('div[id]').attr('id'));
    } else {
      id = this.getId($this.parent().find('div[id]').attr('id'));
    }

    if (tinyMCE.editors[id] !== undefined) {
      return;
    }

    const selectors = this.getSelectors(id);
    const elements = this.getElements(selectors);

    this.closeAllQTags(elements.$iframe);
    this.qtInit(selectors.id);
    this.createTinyMceEditor(id);

    $(selectors.visualTab).removeAttr('onclick').on('click', function(e) {
      e.preventDefault();

      if (typeof window.switchEditors.switchto === 'function') {
        window.switchEditors.switchto(this);
      } else {
        window.switchEditors.go(id, 'tmce');
      }
    });

    $(selectors.htmlTab).removeAttr('onclick').on('click', function(e) {
      e.preventDefault();

      if (typeof window.switchEditors.switchto === 'function') {
        window.switchEditors.switchto(this);
      } else {
        window.switchEditors.go(id, 'html');
      }
    });

    elements.$dom.addClass(selectors.wrap, 'tmce-active');
    elements.$dom.removeClass(selectors.wrap, 'html-active');
  }
}

export default Editor;
