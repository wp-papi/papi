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
  
  /* Collection */
  
  $('a:not(.del)[data-ptb-collection]').on('click', function (e) {
    e.preventDefault();
    
    var $this = $(this)
      , collection = $this.data('ptb-collection')
      , template = $('div.ptb-hidden[data-ptb-collection=' + collection + ']').html()
      , $collection = $('ul[data-ptb-collection=' + collection + ']')
      , i = $collection.find('li').last().data('ptb-collection-i')
      , $li = $('<li />');
    
    i++;
    
    template = template.replace(/name\=\"(\w+)\"/g, function (match, value) {
      var generated = value.replace('ptb_', 'ptb_collection[' + collection + '][' + i +'][') + ']';
      generated = generated.replace(']_property', '_property]');
      return match.replace(value, generated);
    });
    
    $li.html(template);
    $li.attr('data-ptb-collection-i', i);
    
    $collection.append($li);
    
  });
  
  $('ul[data-ptb-collection]').on('click', 'a.del[data-ptb-collection]', function (e) {
    e.preventDefault();
    
    var $this = $(this)
      , collection = $this.data('ptb-collection')
      , $collection = $('ul[data-ptb-collection=' + collection + ']');
    
    $this.closest('li').remove();
    
    var lis = $collection.find('li').size()
      , i = 0;

    $collection.find('li').each(function () {
      var $li = $(this);
      $li.attr('data-ptb-collection-i', i);
      $li.html(ptb_collection_update_i($li.html(), i));
      i++;
    });
  });
  
  /**
   * Update array number in html name.
   *
   * @param {String} html
   * @param {Int} i
   *
   * @return {String}
   */
  
  function ptb_collection_update_i (html, i) {
    return html.replace(/name\=\"(\w+)\"/g, function (match, value) {
      if (match.indexOf('ptb_') !== -1) {
        var generated = value.replace(/\[\d+\]/, '[' + i + ']');
        return match.replace(value, generated);
      }
      return match;
    });
  }
  
  
}(window.jQuery));