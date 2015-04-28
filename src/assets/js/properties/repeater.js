(function ($) {

  // Property repeater

  papi.properties.repeater = {

    template: window.wp.template('papi-property-repeater-row'),

    /**
     * Prepare to add a new row to the repeater
     * and then call fetch to fetch Papi ajax data.
     *
     * @param {object} $this
     */

    add: function ($this) {
      var $repeater = $this.closest('.papi-property-repeater');
      var $tbody    = $repeater.find('> .papi-table tbody');
      var counter   = $tbody.children().length;
      var jsonText  = $($repeater.data().jsonId).text();

      if (!jsonText.length) {
        return;
      }

      var properties = this.prepareProperties(jsonText, counter);
      var self = this;

      this.fetch(properties, function (res) {
        self.addRow($tbody, counter, res);
      });
    },

    /**
     * Add a new row to the repeater.
     *
     * @param {object} $tbody
     * @param {int} counter
     * @param {array} items
     */

    addRow: function ($tbody, counter, res) {
      var columns = [];

      for (var i = 0, l = res.length; i < l; i++) {
        columns.push('<td>' + res[i] + '</td>');
      }

      var $row = this.getHtml({
        columns: columns.join(''),
        counter: counter
      });

      $row.appendTo($tbody);

      // Trigger the property that we just added
      $row
        .find('[name*="_property"]')
        .trigger('papi/property/repeater/added');

      this.scrollDownTable($tbody);
      this.updateDatabaseRowNumber($tbody);
    },

    /**
     * Fetch properties from Papi ajax.
     *
     * @param {array} properties
     * @param {function} callback
     */

    fetch: function (properties, callback) {
        $.ajax({
          type: 'POST',
          data: JSON.stringify(properties),
          url: papi.ajaxUrl + '?action=get_properties',
          dataType: 'json'
        }).success(callback);
    },

    /**
     * Get row html as jQuery object.
     *
     * @param {object} data
     *
     * @return {object}
     */

    getHtml: function (data) {
      var template = this.template;
      template = window._.template(template());
      return $(template(data));
    },

    /**
     * Initialize the repeater.
     */

    init: function () {
      var self = this;

      $('.papi-property-repeater tbody').sortable({
        revert: true,
        handle: '.handle',
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
     * Prepare properties.
     *
     * @param {array} properties
     * @param {int} counter
     */

    prepareProperties: function (properties, counter) {
      var attrNameRegex = /\[(\d+)\]/g;

      properties = $.parseJSON(properties);

      for (var i = 0, l = properties.length; i < l; i++) {
        properties[i].slug = properties[i].slug.replace(attrNameRegex, '[' + counter + ']');
      }

      return properties;
    },

    /**
     * Remove item in the repeater.
     *
     * @param {object} $this
     */

    remove: function ($this) {
      var $tbody = $this.closest('.papi-property-repeater tbody');

      $this.closest('tr').remove();

      this.updateRowNumber($tbody);
    },

    /**
     * Scroll down to table.
     *
     * @param {object} $table
     */

    scrollDownTable: function ($table) {
      $('html, body')
        .animate({
          scrollTop: $('> tr:last', $table).offset().top
        });
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

        $this.find('input, select, textarea').each(function () {
          $this = $(this);

          if ($this.attr('name') === undefined || !$this.attr('name').length) {
            return;
          }

          $this.attr('name', $this.attr('name').replace(/(\[\d+\])/, '[' + index + ']'));
        });
      });

      this.updateDatabaseRowNumber($tbody);
    },

    /**
     * Update database row number.
     *
     * @param {object} $el
     */

    updateDatabaseRowNumber: function ($el) {
      $el
        .closest('.papi-property-repeater')
        .find('.papi-property-repeater-rows')
        .val($el.find('tr').length);
    }

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

})(window.jQuery);
