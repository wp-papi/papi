(function ($) {
  
  'use strict';
  
  /* Tabs */
  
  $('a[data-ptb-tab]').on('click', function (e) {
    e.preventDefault();
    
    var $this = $(this)
      , tab = $this.data('ptb-tab');
    
    $('a[data-ptb-tab]').parent().removeClass('active');
    $this.parent().addClass('active');
    
    $('div[data-ptb-tab]').removeClass('active').hide();
    $('div[data-ptb-tab=' + tab + ']').addClass('active').show();
  });
  
}(window.jQuery));