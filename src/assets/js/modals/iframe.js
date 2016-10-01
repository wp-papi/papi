import $ from 'jquery';

/**
 * Iframe modal.
 */
class Iframe {

  /**
   * The iframe template to use.
   *
   * @return {function}
   */
  get template() {
    const template = [
      '<div tabindex="0" id="papi-iframe-modal-dialog" role="dialog">',
        '<div class="papi-iframe-modal">',
          '<div class="papi-iframe-modal-content">',
            '<div class="papi-iframe-modal-header">',
            '<button type="button" class="papi-iframe-modal-header-close">',
              '<span><span class="screen-reader-text"><%= closeText %></span></span>',
            '</button>',
            '<div class="papi-iframe-modal-header-title">',
              '<h1><%= title %></h1>',
            '</div>',
          '</div>',
          '<iframe id="papi-iframe-modal-frame" src="" frameborder="0" allowtransparency="true"></iframe>',
        '</div>',
      '</div>',
      '<div class="papi-iframe-modal-backdrop" role="presentation"></div>'
    ].join('');

    return window._.template(template);
  }

  /**
   * Initialize Papi iframe modal class.
   */
  static init() {
    if ($('body').hasClass('papi-iframe-mode')) {
      return;
    }

    console.log('iframe init');

    $(document).on('click', 'a.papi-iframe-link-open', function(e) {
      e.preventDefault();

      console.log('iframe open click');

      if (typeof window.papi.iframe !== 'undefined') {
        window.papi.iframe.close();
        window.papi.iframe = undefined;
      }

      const $this = $(this);
      const url   = $this.data('url');

      window.papi.iframe = new Iframe(url);
      window.papi.iframe.open();
    });
  }

  /**
   * Bind elements with functions.
   */
  binds() {
  }

  /**
   * Constructor.
   *
   * @param {string} url
   */
  constructor(url) {
    this.url = url;
    this.$iframe = $(this.template({
      closeText: 'Close',
      title: 'Edit'
    }));
  }

  /**
   * Open iframe modal.
   */
  open() {
    // Add iframe source and load event.
    this.$iframe.find('#papi-iframe-modal-frame')
      .attr('src', this.url)
      .on('load', this.loaded.bind(this));

    // Add click event to close button.
    this.$iframe.find('.papi-iframe-modal-header-close')
      .on('click', this.close.bind(this));

    $('body').css({'overflow': 'hidden'}).append(this.$iframe);
  }

  /**
   * Add iframe events when iframe is loaded.
   *
   * @param  {object} e
   */
  loaded(e) {
    console.log('iframe loaded');

    let $this = $(e.currentTarget);
    let $contents = $this.contents();

    $contents.find('#post').on('submit', this.submit.bind(this));
  }

  /**
   * Change current title in dropdown on iframe submit.
   *
   * @param  {object} e
   */
  submit(e) {
    console.log('iframe submit');

    let $this = $(e.currentTarget);
    let self = this;
    let title = $this.find('[name="post_title"]').val();

    $('a.papi-iframe-link-open[data-url="' + this.url + '"]').prev().text(title);
  }

  /**
   * Close iframe modal.
   *
   * @param  {object} e
   */
  close(e) {
    e.preventDefault();

    console.log('iframe close click');

    $('#papi-iframe-modal-dialog').hide();
    $(document).off('focusin');
    $('body').css({'overflow': 'auto'});
    $('.papi-iframe-modal-close').off('click');
    $('#papi-iframe-modal-dialog').remove();
  }
}

export default Iframe;
