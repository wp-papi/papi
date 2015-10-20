import $ from 'jquery/jquery';

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
      if (self.$el !== undefined) {
        e.preventDefault();
        self.render(window.wpLink.getAttrs());
      }
    });

    $(document).on('click', '.papi-property-link button[data-link-action="edit"]', function (e) {
      e.preventDefault();
      self.edit($(this));
    });

    $(document).on('click', '.papi-property-link button[data-link-action="remove"]', function (e) {
      e.preventDefault();
      self.remove($(this));
    });
  }

  /**
   * Add new link.
   *
   * @param {object} $this
   */
  add($this) {
    this.$el = $this.closest('.papi-property-link');
    this.$p = this.$el.find('p');

    // Create a new window.wpLink.update that only
    // close the wpLink window. Save the old to later.
    this.oldLinkUpdate = window.wpLink.update;
    window.wpLink.update = function () {
      window.wpLink.close();
    };

    window.wpLink.open();
  }

  /**
   * Add new link.
   *
   * @param {object} $this
   */
  edit($this) {
    this.$el = $this.closest('.papi-property-link');
    this.$p = this.$el.find('p');

    window.wpLink.open();

    const url  = this.$el.find('.wp-link-url').val();
    const text = this.$el.find('.wp-link-text').val();

    $('#wp-link-url').val($.trim(url));
    $('#wp-link-text').val($.trim(text));

    if ($.trim(this.$el.find('.wp-link-target').val()) === '_blank') {
      $('#wp-link-target').attr('checked', 'checked');
    }
  }

  /**
   * Remove a link.
   *
   * @param {object} $this
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

    data.link = '<a href="' + data.href + '" target="_blank">' + data.href + '</a>';
    data.title = $('#wp-link-text').val();
    data.slug = this.$el.data('slug');

    this.$el.find('.link-table').remove();
    this.$p.before(template(data));

    const $spans = this.$p.find('> span');
    $spans.first().addClass('papi-hide');
    $spans.last().removeClass('papi-hide');

    // Trigger conditional rule.
    this.$el
      .find('data-papi-rule')
      .trigger('change');

    // Restore window.wpLink.update
    window.wpLink.update = this.oldLinkUpdate;

    this.wpLink = this.$el = this.$p = undefined;
  }

}

export default Link;
