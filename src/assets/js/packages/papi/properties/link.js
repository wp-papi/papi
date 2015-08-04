import $ from 'jquery';
import Utils from 'papi/utils';

/**
 * Property Link.
 *
 * Using the build in link manager in WordPress.
 */

class Link {

  /**
   * The link template to compile.
   *
   * @return {function}
   */

  get template() {
    return window.wp.template('papi-property-link');
  }

  /**
   * Initialize Property Link.
   */

  static init() {
    new Link().binds();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    const self = this;
    $(document).on('click', '.papi-property-link button[data-link-action="add"]', function (e) {
      e.preventDefault();
      self.add($(this));
    });

    $(document).on('click', '#wp-link-submit', function (e) {
      e.preventDefault();
      if (self.$el !== undefined) {
        self.render(wpLink.getAttrs());
      }
    });

    $(document).on('click', '.papi-property-link button[data-link-action="edit"]', function (e) {
      e.preventDefault();
      self.add($(this));
    });

    $(document).on('click', '.papi-property-link button[data-link-action="remove"]', function (e) {
      e.preventDefault();
      self.remove($(this));
    });
  }

  /**
   * Add new link.
   *
   * @param {object} e
   */

  add($this) {
    this.$el = $this.closest('.papi-property-link');
    this.$p  = this.$el.find('p');
    wpLink.open();
  }

  /**
   * Remove a link.
   *
   * @param {object} e
   */

  remove($this) {
    const $prop = $this.closest('.papi-property-link');
    $prop.find('.link-table').remove();

    const $spans = $prop.find('p > span');
    $spans.first().removeClass('papi-hide');
    $spans.last().addClass('papi-hide');

    // Trigger conditional rule.
    $prop
      .find('data-papi-rule')
      .trigger('change');
  }

  /**
   * Render the image with the template.
   *
   * @param {object} data
   */

  render(data) {
    let template = this.template;
    template = window._.template($.trim(template()));

    data.link  = '<a href="' + data.href + '" target="_blank">' + data.href + '</a>';
    data.title = $('#wp-link-text').val();
    data.slug  = this.$el.data('slug');

    this.$el.find('.link-table').remove();
    this.$p.before(template(data));

    const $spans = this.$p.find('> span');
    $spans.first().addClass('papi-hide');
    $spans.last().removeClass('papi-hide');

    // Trigger conditional rule.
    this.$el
      .find('data-papi-rule')
      .trigger('change');

    this.$el = this.$p = undefined;
  }

}

export default Link;
