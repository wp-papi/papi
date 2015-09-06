const wp = window.wp;

class Utils {

  /**
   * Initialize Papi core class.
   */
  static init() {
    new Utils().binds();
  }

  /**
   * Get parameter by name.
   *
   * @param {string} name
   *
   * @return {mixed}
   */
  static getParameterByName(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    let results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
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

          Utils.wpMediaFrame.trigger('insert', attachments[i]);
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
