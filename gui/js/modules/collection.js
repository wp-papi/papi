!function (window, $) {

  // Collection object.
  var Collection = {};

  // Initialize collection.
  Collection.init = function () {
    this.binds();
  };

  // Collection binds.
  Collection.binds = function () {

    // Add new item in the collection.
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

    // Delete item in the collection.
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
        $li.html(Ptb.Utils.update_html_array_num($li.html(), i));
        i++;
      });
    });

    // Move collection item down.
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
        $li.html(Ptb.Utils.update_html_array_num($li.html(), i));
        i++;
      });

    });

    // Move collection item up.
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
        $li.html(Ptb.Utils.update_html_array_num($li.html(), i));
        i++;
      });

    });
  };

  // Add the Collection object to the Ptb object.
  window.Ptb.Collection = Collection;

}(window, window.jQuery);