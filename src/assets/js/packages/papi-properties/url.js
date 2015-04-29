import $ from 'jquery';
import Utils from 'papi/utils';

/**
 * Property Url.
 */

class Url {

  /**
   * Initialize Property Url.
   */

  static init() {
    new Url().binds();
  }

  /**
   * Add new media file.
   *
   * @param {object} e
   */

  add(e) {
    e.preventDefault();

    const $this = $(this);

    Utils.wpMediaEditor().on('insert', (attachment) => {
      $this.prev().val(attachment.url);
    }).open();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $(document).on('click', '.papi-url-media-button', this.add);
  }

}

export default Url;
