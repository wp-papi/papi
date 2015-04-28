const wp = window.wp;

class Utils {

  /**
   * Initialize Papi core class.
   */

  static init() {
    let utils = new Utils();

    utils.binds();
  }

  /**
   * Check if given string is a image via regex.
   *
   * @param {string} url
   */

  static isImage(url) {
    return /\.(jpeg|jpg|gif|png)$/.test(url.toLowerCase());
  }

  /**
   * Open WordPress media editor.
   *
   * @param {object} options
   */

  static wpMediaEditor(options) {
    if (Utils.wpMediaFrame !== undefined) {
      Utils.wpMediaFrame.dispose();
    }

    Utils.wpMediaFrame = wp.media(options)
      .on('select', () => {
        const attachments = Utils.wpMediaFrame.state().get('selection').toJSON();
        for (let i = 0, l = attachments.length; i < l; i++) {
          if (attachments[i] === null) {
            continue;
          }

          Utils.wpMediaFrame.trigger('insert', attachments[i], Utils.isImage(attachments[i].url));
        }
      });

      return Utils.wpMediaFrame;
  }

  /**
   * Get media frame.
   *
   * @return {object}
   */

  static get wpMediaFrame() {
    return this._wpMediaFrame;
  }

  /**
   * Set media frame.
   *
   * @param {object} obj
   */

  static set wpMediaFrame(obj) {
    this._wpMediaFrame = obj;
  }

}

export default Utils;
