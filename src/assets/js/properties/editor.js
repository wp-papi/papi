(function ($) {

  /* global tinymce, tinyMCE, tinyMCEPreInit, QTags */

  'use strict';

  // Property editor

  papi.properties.editor = {

    /**
     * Custom TinyMCE settings.
     *
     * @var {object}
     */

    customTinyMCESettings: {
      elements: [],
      mode: 'exact',
      /* jshint ignore:start */
      theme_advanced_resizing: true
      /* jshint ignore:end */
    },

    /**
     * Get elements.
     *
     * @param {object} selectors
     *
     * @return {object}
     */

    getElements: function(selectors) {
      var $dom = tinyMCE.DOM;
      return {
        $dom: $dom,
        $iframe: $('#' + selectors.iframe)
      };
    },

    /**
     * Get TinyMCE Id.
     *
     * @param {string} id
     *
     * @return {string}
     */

    getId: function (id) {
      return id.replace('wp-', '').replace('-wrap', '');
    },

    /**
     * Get selectors.
     *
     * @param {string} id
     *
     * @return {object}
     */

    getSelectors: function (id) {
      return {
        id: id,
        iframe: id + '_ifr',
        htmlTab: '#' + id + '-html',
        visualTab: '#' + id + '-tmce',
        wrap: 'wp-' + id + '-wrap'
      };
    },

    /**
     * Close all QTags.
     *
     * @param {object} $iframe
     */

    closeAllQTags: function ($iframe) {
      if (typeof(QTags) === undefined || $iframe.canvas === undefined) {
        return;
      }

      QTags.closeAllTags($iframe.id);
    },

    /**
     * Initialize QTags.
     *
     * @param {string} id
     */

    qtInit: function(id) {
      var qtContent = tinyMCEPreInit.qtInit.content;
      var qtInit = tinyMCEPreInit.qtInit[id] = $.extend({}, qtContent, {
        id: id,
        buttons: qtContent.buttons.replace(',fullscreen', '')
      });

      try {
        new QTags(qtInit);
      } catch (e) {}

      QTags._buttonsInit();
    },

    /**
     * Get TinyMCE editor.
     *
     * @param {string} id
     *
     * @return {object}
     */

    createTinyMceEditor: function(id) {
      var mceInit;

      if (!tinyMCEPreInit.mceInit[id]) {
        mceInit = tinyMCEPreInit.mceInit[id] = $.extend({}, tinyMCEPreInit.mceInit.content);
      } else {
        mceInit = tinyMCEPreInit.mceInit[id];
      }

      mceInit = $.extend(mceInit, this.customTinyMCESettings, {
        selector: '#' + id,
        elements: id
      });

      tinymce.init(mceInit);

      //return new tinymce.Editor(id, mceInit);
    },

    /**
     * Update editor when it is added to repeater.
     *
     * @param {object} $this
     */

    update: function ($this) {
      var id = this.getId($this.parent().find('div[id]').attr('id'));

      if (tinyMCE.editors[id] !== undefined) {
        return;
      }

      var selectors = this.getSelectors(id);
      var elements = this.getElements(selectors);

      this.closeAllQTags(elements.$iframe);
      this.qtInit(selectors.id);
      this.createTinyMceEditor(id);

      $(selectors.visualTab).removeAttr('onclick').on('click', function (e) {
        e.preventDefault();
        window.switchEditors.switchto(this);
      });

      $(selectors.htmlTab).removeAttr('onclick').on('click', function (e) {
        e.preventDefault();
        window.switchEditors.switchto(this);
      });

			elements.$dom.addClass(selectors.wrap, 'tmce-active');
			elements.$dom.removeClass(selectors.wrap, 'html-active');
    }

  };

  // Events

  $(document).on('papi/property/repeater/added', '[value="editor"]', function (e) {
    e.preventDefault();

    papi.properties.editor.update($(this));
  });

})(window.jQuery);
