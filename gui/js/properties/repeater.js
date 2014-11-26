(function ($) {

  // Property repeater

  papi.properties.repeater = {

    /**
     * Add a new row to the repeater.
     *
     * @param {object} $this
     */

    add: function ($this) {
      var $repeater     = $this.closest('.papi-property-repeater'),
          $template     = $('.repeater-template tr', $repeater).clone(),
          $table        = $repeater.find('> .papi-table tbody'),
          counter       = $table.children().length,
          dataNameRegex = /data\-name\=/g,
          attrNameRegex = /\[(\d+)\]/g,
          attrNameValue = '[' + counter + ']',
          html          = $template.html().replace(dataNameRegex, 'name=');

      $template.html(html);

      // Update array number in html name and name if ends with a number.
      $template.find('[name],[data-slug], [data-id]').each(function () {
        var $this = $(this),
            attrs = [
              {
                source: 'data-slug',
                target: 'data-slug',
              },
              {
                source: 'data-id',
                target: 'id'
              },
              {
                source: 'name',
                target: 'name'
              }
            ],
            attr  = '',
            valeu = '';

        for (var i = 0, l = attrs.length; i < l; i++) {
          if ($this.attr(attrs[i].source) !== undefined) {
            attr  = attrs[i].target;
            value = $this.attr(attrs[i].source);
          }
        }

        value = value.replace(attrNameRegex, attrNameValue);

        $this.attr(attr, value);
      });

      $template.find('td:first-child span').text(counter + 1);

      $template.appendTo($table);

      // Trigger the property that we just added
      $template.find('[name*="_property"]').trigger('papi_property_repeater_added');

      $('html, body').animate({
        scrollTop: $('> tr:last', $table).offset().top
      });

      $table
        .closest('.papi-property-repeater')
        .find('.papi-property-repeater-rows')
        .val($table.find('tr').length);
    },

    /**
     * Initialize the repeater.
     *
     * @param {object} $this
     */

    init: function ($this) {
      var self = this;

      $('.repeater-template [name]').each(function () {
        var $this = $(this);
        $this.attr('data-name', $this.attr('name'));
        $this.removeAttr('name');
      });

      $('.papi-property-repeater tbody').sortable({
        revert: true,
        helper: function (e, ui) {
          ui.children().each(function() {
            $(this).width($(this).width());
          });
          return ui;
        },
        stop: function () {
          self.updateRowNumber($(this).closest('.papi-property-repeater tbody'));
        }
      });
    },

    /**
     * Remove item in the repeater.
     *
     * @param {object} $this
     */

    remove: function ($this) {
      var $tbody = $this.closest('.papi-property-repeater tbody');

      $this.closest('tr').remove();

      this.updateRowNumber($tbody);
    },

    /**
     * Update table row number.
     *
     * @param {object} $tbody
     */

    updateRowNumber: function ($tbody) {
      $tbody.find('tr').each(function (index) {
        var $this = $(this);

        $this.find('td:first-child span').text(index + 1);

        $this.find('input,select,textarea').each(function () {
          var $this = $(this);

          if ($this.attr('name') === undefined || !$this.attr('name').length) {
            return;
          }

          $this.attr('name', $this.attr('name').replace(/(\[\d+\])/, '[' + index + ']'));
        });
      });

      $tbody
        .closest('.papi-property-repeater')
        .find('.papi-property-repeater-rows')
        .val($tbody.find('tr').length);
    },

  };

  // Events

  $(document).on('click', '.papi-property-repeater .bottom a.button', function (e) {
    e.preventDefault();

    papi.properties.repeater.add($(this));
  });

  $(document).on('click', '.papi-property-repeater .repeater-remove-item', function (e) {
    e.preventDefault();

    papi.properties.repeater.remove($(this));
  });

  })(jQuery);
