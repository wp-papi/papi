import $ from 'jquery';
import select2Options from 'components/select2';

/**
 * Property Post.
 *
 * Using Select2.
 */
class Post {

  /**
   * The option template to compile.
   *
   * @return {function}
   */
  get optionTemplate() {
    return window.wp.template('papi-property-post-option');
  }

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
    $(document).on('papi/property/repeater/added', '[data-property="post"]', this.update.bind(this));
    $(document).on('change', '.papi-property-post-left', this.change.bind(this));
    $(document).on('papi/iframe/submit', this.iframeSubmit.bind(this));
  }

  /**
   * Change post dropdown when selecting
   * a different post type.
   *
   * @param {object} e
   */
  change(e) {
    e.preventDefault();

    const $this = $(e.currentTarget);
    const query = $this.data('post-query').length
      ? $this.data('post-query')
      : {};

    query.post_type = $this.val();

    const params  = {
      'action': 'get_posts',
      'fields': ['ID', 'post_title'],
      'query': query
    };
    const $prop   = $this.closest('.papi-property-post');
    const $select = $prop.find('.papi-property-post-right');

    $('[for="' + $select.attr('id') + '"]')
      .parent()
      .find('label')
      .text($this.data('select-item').replace('%s', $this.find('option:selected').text()));

    $.get(papi.ajaxUrl + '?' + $.param(params), function(posts) {
      $select.empty();

      $.each(posts, function(index, post) {
        $select.append($('<option></option>').attr('value', post.ID).text(post.post_title));
      });

      if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
        $select.select2();
      }
    });
  }

  /**
   * Update select when iframe is submitted.
   *
   * @param {object} e
   * @param {object} data
   */
  iframeSubmit(e, data) {
    if (!data.iframe) {
      return;
    }

    const $elm = $(data.iframe);
    const title = $elm.find('[name="post_title"]').val();
    const id = $elm.find('[name="post_ID"]').val();
    const $select = $('[name=' + data.selector + ']');

    if (data.url.indexOf('post-new') !== -1) {
      // new
      let optionTemplate = this.optionTemplate;
      let template = window._.template($.trim(optionTemplate()));

      if ($select.find('option[value=' + id + ']').length) {
        return;
      }

      $select.append(template({
        id: id,
        title: title,
      }));
    } else {
      // edit
      const $option = $select.find('option[data-edit-url="' + data.url + '"]');
      $option.removeData('data');
      $option.text(title);
    }

    $select.trigger('change');
    $select.val(id);
  }

  /**
   * Initialize pikaday field when added to repeater.
   *
   * @param {object} e
   */
  update(e) {
    e.preventDefault();

    const $select = $(e.currentTarget).parent().find('select');

    if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
      $select.select2();
    }
  }
}

export default Post;
