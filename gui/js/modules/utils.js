(function () {

  // Utils object.
  var utils = {};

  /**
   * Open WordPress media editor.
   *
   * @param {object} options
   * @param {object} $target
   */

  utils.wpMediaEditor = function (options, $target) {

    if (typeof options === 'function' || options instanceof jQuery) {
      $target = options;
      options = {
        multiple: false
      };
    }

    var uploader = wp.media(options).on('select', function () {
      var attachments = uploader.state().get('selection').toJSON();
      for (var i = 0, l = attachments.length; i < l; i++) {
        if (attachments[i] === null) {
          continue;
        }

        if (typeof $target === 'function') {
          $target(attachments[i], utils.isImage(attachments[i].url));
        } else {
          $target.val(attachments[i].url);
        }
      }
    }).on('escape', function () {
      if (typeof $target === 'function') {
        $target(null, false);
      }
    }).open();
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
  }

  // Add utils to the papi object.
  window.papi.utils = utils;

}());
