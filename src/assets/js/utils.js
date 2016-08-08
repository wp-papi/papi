import $ from 'jquery';

const wp = window.wp;

class Utils {

  /**
   * Initialize Papi core class.
   */
  static init() {
    new Utils().binds();
  }

  /**
   * Get meta type.
   *
   * @return {string}
   */
  static getMetaType() {
    const $body = $('body');

    if ($body.hasClass('papi-meta-type-term')) {
      return 'term';
    }

    if ($body.hasClass('papi-meta-type-post')) {
      return 'post';
    }

    if (/page=.*papi(?:%2F|\/)option/.test(window.location.search)) {
      return 'option';
    }
  }

  /**
   * Get meta type key.
   *
   * @return {string}
   */
  static getMetaTypeKey() {
    switch (Utils.getMetaType()) {
      case 'post':
        return 'post_type';
      case 'term':
        return 'taxonomy';
      default:
        break;
    }
  }

  /**
   * Get meta type value.
   *
   * @return {string}
   */
  static getMetaTypeValue() {
    switch (Utils.getMetaType()) {
      case 'post':
        const value = Utils.getParameterByName('post_type');

        if (value.length) {
          return value;
        }

        return $('#post_type').val();
      case 'term':
        return Utils.getParameterByName('taxonomy');
      default:
        break;
    }
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
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(window.location.search);
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
