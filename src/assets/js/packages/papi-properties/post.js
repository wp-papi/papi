import $ from 'jquery';

/**
 * Property Post.
 *
 * Using Select2.
 */

class Post {

  /**
   * Initialize Property Post.
   */

  static init() {
    new Post().binds();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $(document).on('papi/property/repeater/added', '[value="post"]', this.update);
  }

  /**
   * Initialize pikaday field when added to repeater.
   *
   * @param {object} e
   */

  update(e) {
    e.preventDefault();

    const $select = $(this).parent().find('select');

    if ($select.hasClass('papi-vendor-select2') && typeof $select.select2 === 'function') {
      $select.select2();
    }
  }
}

export default Post;
