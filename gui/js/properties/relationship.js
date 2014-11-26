(function ($) {

  // Property relationship

  papi.properties.relationship = {

    /**
     * Add page to list of selected pages.
     *
     * @param {object} $this
     */

    add: function ($this) {
      var $li     = $this.clone(),
          $right  = $this.closest('.papi-property-relationship').find('.relationship-right'),
          $list   = $right.find('ul'),
          max     = $right.data('relationship-choose-max'),
          append  = max === undefined || max === -1 || $list.find('li').length < max;

      if (append) {
        $li.find('span.icon').removeClass('plus').addClass('minus');
        $li.find('input').attr('name', $li.find('input').data('name'));

        $li.appendTo($list);
      }
    },

    /**
     * Remove the selected page.
     *
     * @param {object} $this
     */

    remove: function ($this) {
      $this.remove();
    },

    /**
     * Search for a page in the list.
     *
     * @param {object} $this
     */

    search: function ($this) {
      var $list = $this.closest('.papi-property-relationship').find('.relationship-left ul'),
          val   = $this.val().toLowerCase();

      $list.find('li').each(function () {
        var $li = $(this);
        $li[$li.text().toLowerCase().indexOf(val) === -1 ? 'hide' : 'show']();
      });
    },

    /**
     * Initialize pikaday field when added to repeater.
     *
     * @param object $this
     */

    updateSelect: function ($this) {
      var $prop = $this.prev();

      $prop.find('.relationship-left [name]').each(function () {
        var $this = $(this);
        $this.attr('data-name', $this.attr('name'));
        $this.removeAttr('name');
      });
    }

  };

  // Events

  $(document).on('click', '.papi-property-relationship .relationship-left li', function (e) {
    e.preventDefault();

    papi.properties.relationship.add($(this));
  });

  $(document).on('click', '.papi-property-relationship .relationship-right li', function (e) {
    e.preventDefault();

    papi.properties.relationship.remove($(this));
  });

  $(document).on('keyup', '.papi-property-relationship input[type=search]', function (e) {
    e.preventDefault();

    papi.properties.relationship.search($(this));
  });

  $(document).on('papi_property_repeater_added', '[value="relationship"]', function (e) {
    e.preventDefault();

    papi.properties.relationship.updateSelect($(this));
  });

})(jQuery);
