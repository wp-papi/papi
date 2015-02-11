(function ($) {

  'use strict';

  // Required fields

  $('body').on('click', '.papi-rq-link', function (e) {
    e.preventDefault();

    $('html, body')
      .animate({
        scrollTop: $('[for=' + $(this).attr('href').replace('#', '') + ']').offset().top - 45
      });
  });

  $('#publish').on('click', function (e) {

    var $button = $(this);
    var $fields = $('.papi-rq');
    var $spinner = $('#publishing-action .spinner');
    var $errors = [];
    for (var i = 0, l = $fields.length; i < l; i++) {

      var $this = $($fields[i]);

      if ($this.parent().parent().hasClass('metabox-prefs') || !$this.is(':visible')) {
        continue;
      }

      var $field = $('[name="' + $this.attr('data-property-id') + '"]');

      if (!$field.length) {
        $field = $('[name="' + $this.attr('data-property-id') + '[]"]').first();
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

      var items = '';

      for (var i = 0, l = $errors.length; i < l; i++) {
        var $field = $($errors[i]);
        items += '<a class="papi-rq-link" href="#' + $field.attr('data-property-id') + '">' + $field.attr('data-property-name') + '</a>';

        if (i + 1 !== $errors.length) {
          items += ', ';
        }
      }

      $('.wrap h2').after('<div id="message" class="error below-h2"><p>' + window.papiL10n.requiredError + ' ' + items + '</p></div>');
    }

  });

})(window.jQuery);
