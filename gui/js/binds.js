(function ($) {

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
        $fields = $('.papi-rq').closest('label'),
        $spinner = $('#publishing-action .spinner'),
        $errors = [];

    for (var i = 0, l = $fields.length; i < l; i++) {

      var $this = $($fields[i]),
          $field = $('[name="' + $this.attr('for') + '"]');

      if (!$field.length) {
        console.log($this.attr('for'), '[name*="' + $this.attr('for') + '[]"]');
        $field = $('[name="' + $this.attr('for') + '[]"]').first();
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
        items += '<a class="papi-rq-link" href="#' + $field.attr('for') + '">' + $field.clone().children().remove().end().text().trim() + '</a>';

        if (i + 1 !== $errors.length) {
          items += ', ';
        }
      }

      $('.wrap h2').after('<div id="message" class="error below-h2"><p>' + papiL10n.requiredError + ' ' + items + '</p></div>');
    }

  });

})(jQuery);
