import $ from 'jquery/jquery.js';


class Required {

  /**
   * Initialize Papi core class.
   */

  static init() {
    new Required().binds();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $('body').on('click', '.papi-rq-link', this.requiredLink);
    $('#publish').on('click', this.publishPost);
  }

  /**
   * Animate down to required field.
   *
   * @param {object} e
   */

  requiredLink(e) {
    $('html, body').animate({
      scrollTop: $('[for=' + $(this).attr('href').replace('#', '') + ']').offset().top - 45
    });
  }

  /**
   * Collect all required fields that don't have any value
   * and output error message.
   *
   * @param {object} e
   */

  publishPost(e) {
    const $button  = $(this);
    const $fields  = $('.papi-rq');
    const $spinner = $('#publishing-action .spinner');
    let   $errors  = [];

    for (let i = 0, l = $fields.length; i < l; i++) {
      let $this = $($fields[i]);

      if ($this.parent().parent().hasClass('metabox-prefs') || !$this.is(':visible')) {
        continue;
      }

      let data = $this.data();
      let $field = $('[name="' + data.propertyId + '"]');

      if (!$field.length) {
        $field = $('[name="' + data.propertyId + '[]"]').first();
      }

      if ($field.val() === undefined || !$field.val().length) {
        $errors.push($fields[i]);
      }
    }

    if ($errors.length) {
      e.preventDefault();

      $spinner.hide();
      $button.removeClass('button-primary-disabled');
      $('#message').remove();

      let items = '';

      for (let i = 0, l = $errors.length; i < l; i++) {
        let $field = $($errors[i]);
        let data = $field.data();
        items += '<a class="papi-rq-link" href="#' + data.propertyId + '">' + data.propertyName + '</a>';

        if (i + 1 !== $errors.length) {
          items += ', ';
        }

        $('.wrap h2').after('<div id="message" class="error below-h2"><p>' + window.papiL10n.requiredError + ' ' + items + '</p></div>');
      }
    }
  }

}

export default Required;
