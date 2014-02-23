(function ($) {
  
  'use strict';
  
  /* Page Type Builder object */
  
  window['ptb'] = {
  
    /**
     * Update array number in html name.
     *
     * @param {String} html
     * @param {Int} i
     *
     * @return {String}
     */

    collection_update_i: function (html, i) {
      return html.replace(/name\=\"(\w+)\"/g, function (match, value) {
        if (match.indexOf('ptb_') !== -1) {
          var generated = value.replace(/\[\d+\]/, '[' + i + ']');
          return match.replace(value, generated);
        }
        return match;
      });
    },
  
    /**
     * Hook up the WordPress media editor.
     *
     * @param {Object} $button
     * @param {Object|Function} $target
     */

    wp_media_editor: function ($button, $target) {
      wp.media.editor.send.attachment = function (props, attachment) {
        if (typeof $target === 'function') {
          $target(attachment);
        } else {
          $target.val(attachment.url);
        }
      };
      wp.media.editor.open($button);
    },
    
    /**
     * Check if given string is a image via regex.
     *
     * @param {String} url
     *
     * @return {Bool}
     */
    
    is_image: function (url) {
      return /\.(jpeg|jpg|gif|png)$/.test(url.toLowerCase());
    }
    
  };
  
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
  
  $('a.add-new[data-ptb-collection]').on('click', function (e) {
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
  
  /* Collection - Delete */
  
  $('ul[data-ptb-collection]').on('click', 'a.del[data-ptb-collection]', function (e) {
    e.preventDefault();
    
    var $this = $(this)
      , collection = $this.data('ptb-collection')
      , $collection = $('ul[data-ptb-collection=' + collection + ']')
      , i = 0;
    
    $this.closest('li').remove();

    $collection.find('li').each(function () {
      var $li = $(this);
      $li.attr('data-ptb-collection-i', i);
      $li.html(ptb.collection_update_i($li.html(), i));
      i++;
    });
  });
  
  /* Collection - Move Down */
  
  $('ul[data-ptb-collection]').on('click', 'a.down[data-ptb-collection]', function (e) {
    e.preventDefault();
    
    var $this = $(this)
      , collection = $this.data('ptb-collection')
      , $collection = $('ul[data-ptb-collection=' + collection + ']')
      , $li = $this.closest('li')
      , i = 0;
    
    $li.next().after($li);

    $collection.find('li').each(function () {
      var $li = $(this);
      $li.attr('data-ptb-collection-i', i);
      $li.html(ptb.collection_update_i($li.html(), i));
      i++;
    });
    
  });
  
  /* Collection - Move Up */
  
  $('ul[data-ptb-collection]').on('click', 'a.up[data-ptb-collection]', function (e) {
    e.preventDefault();
    
    var $this = $(this)
      , collection = $this.data('ptb-collection')
      , $collection = $('ul[data-ptb-collection=' + collection + ']')
      , $li = $this.closest('li')
      , i = 0;
    
    $li.prev().before($li);

    $collection.find('li').each(function () {
      var $li = $(this);
      $li.attr('data-ptb-collection-i', i);
      $li.html(ptb.collection_update_i($li.html(), i));
      i++;
    });
  
  });
  
  /* Add new page search */
  
  $('input[name=add-new-page-search]').on('keyup', function (e) {
    
    var $this = $(this)
      , $list = $('.ptb-box-list')
      , val = $(this).val();
    
    $list.find('li').each(function () {
      var $li = $(this);
      $li[$li.text().indexOf(val) === -1 ? 'hide' : 'show']();
    });
    
  });
  
}(window.jQuery));