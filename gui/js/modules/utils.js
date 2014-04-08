!function (window) {

  // Utils object.
  var Utils = {};

  /**
   * Hook up the WordPress media editor.
   *
   * @param {Object} $button
   * @param {Object|Function} $target
   */

  Utils.wp_media_editor = function ($button, $target) {
    wp.media.editor.send.attachment = function (props, attachment) {
      if (typeof $target === 'function') {
        $target(attachment);
      } else {
        $target.val(attachment.url);
      }
    };
    wp.media.editor.open($button);
  };

  /**
   * Check if given string is a image via regex.
   *
   * @param {String} url
   *
   * @return {Bool}
   */

  Utils.is_image = function (url) {
    return /\.(jpeg|jpg|gif|png)$/.test(url.toLowerCase());
  };

  // Add the Utils object to the Ptb object.
  window.Ptb.Utils = Utils;

}(window);