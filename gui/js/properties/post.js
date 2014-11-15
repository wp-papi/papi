(function ($) {

  // Property post

  papi.properties.post = {

    /**
     * Open thickbox.
     *
     * @param {object} $this
     */

    open: function ($this) {
      $this.next().trigger('click');
    },

    /**
     * .
     *
     * @param {object} $this
     */

    remove: function ($this) {
      var $prop = $this.closest('.papi-property-post'),
          $post = $this.closest('.papi-post-value');

      $prop.find('.papi-post-select').show();

      $post.html('');
    },

    /**
     * Search for a post in the list.
     *
     * @param {object} $this
     */

    search: function ($this) {
      var $list = $this.closest('.papi-property-post').find('.papi-post-list'),
          val   = $this.val().toLowerCase();

      $list.find('li').each(function () {
        var $li = $(this);
        $li[$li.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
      });
    },

    /**
     * Select post and append the view.
     *
     * @param {object} $this
     */

    select: function ($this) {
      var $tmpl   = $this.closest('.papi-property-post.thickbox'),
          $prop   = $('[data-slug="' + $tmpl.data().slug + '"]'),
          $target = $prop.find('.papi-post-value');

      tb_remove();

      $prop.find('.papi-post-select').hide();
      $target.show();

      new papi.view.Post({
        el: $target
      }).render({
        id: $this.data().id,
        slug: $prop.data().slug,
        title: $this.text()
      });
    }

  };

  // Events

  $(document).on('click', '.papi-property-post .papi-post-select > .button', function (e) {
    e.preventDefault();

    papi.properties.post.open($(this));
  });

  $(document).on('click', '.papi-property-post .papi-post-value a', function (e) {
    e.preventDefault();

    papi.properties.post.remove($(this));
  });

  $(document).on('keyup', '.papi-property-post input[type=search]', function (e) {
    e.preventDefault();

    papi.properties.post.search($(this));
  });

  $(document).on('click', '.papi-property-post.thickbox .papi-post-list a', function (e) {
    e.preventDefault();

    console.log($(this));

    papi.properties.post.select($(this));
  });

})(jQuery);
