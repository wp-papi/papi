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
    $(document).on('papi/property/repeater/added', '[data-property="post"]', this.update);
    $(document).on('change', '.papi-property-post-left', this.change);
  }

  /**
   * Change post dropdown when selecting
   * a different post type.
   *
   * @param {object} e
   */
  change(e) {
    e.preventDefault();

    const $this = $(this);
    const query = $this.data('post-query').length
      ? $this.data('post-query')
      : {};

    query.post_type = $this.val();

    const params = {
      'action': 'get_posts',
      'fields': ['ID', 'post_title'],
      'query': query
    };
    const $prop = $this.closest('.papi-property-post');
    const $select = $prop.find('.papi-property-post-right');

    $('[for="' + $select.attr('id') + '"]')
      .parent()
      .find('label')
      .text($this.data('select-item').replace('%s', $this.find('option:selected').text()));

    $.get(papi.ajaxUrl + '?' + $.param(params), function(posts) {
      $select.empty();

      $.each(posts, function(index, post) {
        $select
          .append($('<option></option>')
          .attr('value', post.ID)
          .text(post.post_title));
      });

      if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
        $select.select2();
      }
    });
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
