import $ from 'jquery';

class Oembed {

  add() {
    const $this   = $(this);
    const $target = $this.parent().parent().find('.papi-oembed-bottom');
    const url     = $this.val();
    const param   = $.param({
      action: 'handle_oembed_ajax',
      height: 390,
      property: 'oembed',
      width: 640,
      url: url
    });

    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: papi.ajaxUrl + '?' + param
    }).success(function(res) {
      if (res.success) {
        $target.html(res.html).removeClass('loading');
      }
    });
  }

  binds() {
    $(document).on('change keyup paste', '.papi-property-oembed input', this.add);
  }

  static init() {
    new Oembed().binds();
  }

}

export default Oembed;
