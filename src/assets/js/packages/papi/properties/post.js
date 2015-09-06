import $ from 'jquery/jquery.js';


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
    $(document).on('papi/property/repeater/added', '[data-property="post"]', this.update);
  }

  /**
   * Initialize pikaday field when added to repeater.
   *
   * @param {object} e
   */

  update(e) {
    e.preventDefault();

    const $select = $(this).parent().find('select');

    if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
      $select.select2();
    }
  }
}

export default Post;
