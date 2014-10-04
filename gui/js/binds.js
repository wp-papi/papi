(function ($) {

  // Use WordPress media uploader to set files on property url
  // when `data-papi-action` has the action `mediauploader`
  $('body').on('click', '[data-papi-action="mediauploader"]', function (e) {
    e.preventDefault();

    var $target = $(this).prev();

    papi.Utils.wpMediaEditor($target);
  });

  // Use Pikaday for property date.
  if (window.Pikaday !== undefined) {
    new Pikaday({
      field: $('input[data-papi-property="date"]')[0],
      format: 'YYYY-MM-DD',
      setDefaultDate: true
    });
  }

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


// <div id="message" class="updated below-h2"><p>Page published. <a href="http://dev.isopress.com/?page_id=44">View page</a></p></div>