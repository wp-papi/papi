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

  /**
   * Update array number in html name.
   *
   * @param {String} html
   * @param {Int} i
   *
   * @return {String}
   */

  Utils.update_html_array_num = function (html, i) {
    return html.replace(/name\=\"(\w+)\"/g, function (match, value) {
      if (match.indexOf('ptb_') !== -1) {
        var generated = value.replace(/\[\d+\]/, '[' + i + ']');
        return match.replace(value, generated);
      }
      return match;
    });
  };

  // Add the Utils object to the Ptb object.
  window.Ptb.Utils = Utils;

}(window);