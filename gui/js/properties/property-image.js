!function ($) {

  $('.ptb-property-image .pr-images').on('click', 'li', function (e) {
    e.preventDefault();

    var $this = $(this)
      , $target = $this
      , $li = $this.closest('li')
      , $img = $li.find('img')
      , remove = $img.attr('src') !== undefined && $li.find('p.pr-remove-image').length && e.target.tagName.toLowerCase() === 'a';

    if ($li.hasClass('pr-add-new')) {
      $target = $this.closest('.ptb-property-image').find('.pr-template > li:first').clone();
      $target.insertBefore($li);
      $target = $target.find('img');
    } else if (!remove) {
      $target = $img;
    }

    if (remove) {
      $li = $target.closest('li');

      if (!$li.data('ptb-gallery')) {
        $target = $this.closest('.ptb-property-image').find('.pr-template > li:first').clone();
        $target.insertBefore($li);
      }

      $li.remove();
    } else {
      Ptb.Utils.wp_media_editor(function (attachment) {
        if (attachment !== undefined && Ptb.Utils.is_image(attachment.url)) {
          $target.attr('style', 'height:auto');
          $target.attr('src', attachment.url);
          $target.next().val(attachment.id);
          $target.closest('li').addClass('pr-image-item');
          $target.find('.pr-remove-image').show();
        } else {
          var $li = $target.closest('li')
            , $img = $li.find('img');

          if ($img.attr('src') === undefined || $img.attr('src') === '') {
            if ($li.data('ptb-gallery')) {
              $li.remove();
            } else {
              $target = $this.closest('.ptb-property-image').find('.pr-template > li:first').clone();
              $target.insertBefore($li);
              $li.remove();
            }
          }
        }
      });
    }
  });

}(window.jQuery);