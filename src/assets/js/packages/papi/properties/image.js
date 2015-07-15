import $ from 'jquery';
import Utils from 'papi/utils';

/**
 * Property Image.
 *
 * Using the build in media management in WordPress.
 */

class Image {

  /**
   * The image template to compile.
   *
   * @return {function}
   */

  get template() {
    return window.wp.template('papi-property-image');
  }

  /**
   * Initialize Property Image.
   */

  static init() {
    new Image().binds();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $('.inside .papi-table:not(.papi-table-template) > tbody .papi-property-image.gallery .attachments').sortable({
      revert: true
    });

    const self = this;
    $(document).on('click', '.papi-property-image .papi-image-select > .button', function (e) {
      e.preventDefault();
      self.add($(this));
    });

    $(document).on('mouseenter mouseleave', '.papi-property-image .attachment', this.hover);
    $(document).on('click', '.papi-property-image .attachment a', this.remove);
    $(document).on('papi/property/repeater/added', '[data-property="image"]', this.update);
    $(document).on('click', '.papi-property-image .attachment', this.replace);
  }

  /**
   * Add new image.
   *
   * @param {object} e
   */

  add($this) {
    const $prop    = $this.closest('.papi-property-image');
    const $select  = $this.closest('p');
    const $target  = $prop.find('.attachments');
    const multiple = $prop.hasClass('gallery');
    const slug     = $this.data().slug;
    const self     = this;

    Utils.wpMediaEditor({
      library: {
        type: 'image'
      },
      multiple: multiple
    }).on('insert', (attachment) => {
      let url = attachment.url;

      if (attachment.sizes !== undefined && attachment.sizes.thumbnail !== undefined) {
        url = attachment.sizes.thumbnail.url;
      }

      self.render($target, {
        alt:   attachment.alt,
        id:    attachment.id,
        image: url,
        slug:  slug
      });

      if (!multiple) {
        $select.addClass('papi-hide');
      }
    }).open();
  }

  /**
   * Toggle the remove button.
   *
   * @param {object} e
   */

  hover(e) {
    e.preventDefault();

    $(this).find('a').toggle();
  }

  /**
   * Remove a image.
   *
   * @param {object} e
   */

  remove(e) {
    e.stopPropagation();
    e.preventDefault();

    const $this = $(this);

    $this.closest('.papi-property-image')
      .find('.papi-image-select')
      .removeClass('papi-hide');

    $this.closest('.attachment')
      .remove();
  }

  /**
   * Render the image with the template.
   *
   * @param {object} $el
   * @param {object} data
   */

  render($el, data) {
    let template = this.template;
    template = window._.template(template());
    $el.append('<div class="attachment">' + template(data) + '</div>');
  }

  /**
   * Replace image with another one.
   *
   * @param {object} e
   */

  replace(e) {
    e.preventDefault();

    const $this  = $(this);
    const $img   = $this.find('img[src]');
    const $input = $this.find('input[type=hidden]');
    const postId = $input.val();

    Utils.wpMediaEditor({
      library: {
        type: 'image'
      },
      multiple: false
    }).on('open', () => {
      let   selection = Utils.wpMediaFrame.state().get('selection');
      const attachment = window.wp.media.attachment(postId);

      attachment.fetch();
      selection.add(attachment ? [attachment] : []);
    }).on('insert', (attachment) => {
      let url = attachment.url;

      if (attachment.sizes !== undefined && attachment.sizes.thumbnail !== undefined) {
        url = attachment.sizes.thumbnail.url;
      }

      $img.attr('src', url).attr('alt', attachment.alt);
      $input.val(attachment.id);
    }).open();
  }

  /**
   * Update when added to repeater.
   */

  update(e) {
    e.preventDefault();

    $(this).prev()
      .find('.attachments')
      .sortable({
        revert: true
      });
  }

}

export default Image;
