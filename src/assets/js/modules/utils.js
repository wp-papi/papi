(function () {

  'use strict';

  // Utils object.
  var utils = {
    wpMediaFrame: undefined
  };

  /**
   * Open WordPress media editor.
   *
   * @param {object} options
   */

  utils.wpMediaEditor = function (options) {
    // Destroy the previous frame if it exists.
    if (utils.wpMediaFrame !== undefined) {
      utils.wpMediaFrame.dispose();
    }

    utils.wpMediaFrame = window.wp.media(options).on('select', function () {
      var attachments = utils.wpMediaFrame.state().get('selection').toJSON();
      for (var i = 0, l = attachments.length; i < l; i++) {
        if (attachments[i] === null) {
          continue;
        }

        utils.wpMediaFrame.trigger('insert', attachments[i], utils.isImage(attachments[i].url));
      }
    }).on('escape', function () {
      if (typeof $target === 'function') {
        utils.wpMediaFrame.trigger('insert', null, false);
      }
    });

    return utils.wpMediaFrame;
  };

  /**
   * Check if given string is a image via regex.
   *
   * @param {string} url
   */

  utils.isImage = function (url) {
    return /\.(jpeg|jpg|gif|png)$/.test(url.toLowerCase());
  };

  /**
   * Slugify the given string.
   *
   * @param {string} str
   */

  utils.slugify = function (str) {
    return str.toString().toLowerCase()
          .replace(/\s+/g, '-')
          .replace(/[^\w\-]+/g, '')
          .replace(/\-\-+/g, '-')
          .replace(/^-+/, '')
          .replace(/-+$/, '');
  };

  // Add utils to the papi object.
  window.papi.utils = utils;

}());
