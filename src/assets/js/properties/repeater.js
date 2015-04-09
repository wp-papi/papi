(function ($, papiL10n) {

  'use strict';

  // Property repeater

  papi.properties.repeater = {

    /**
     * Add a new row to the repeater.
     *
     * @param {object} $this
     */

    add: function ($this) {
      var $repeater     = $this.closest('.papi-property-repeater');
      var $table        = $repeater.find('> .papi-table tbody');
      var counter       = $table.children().length;
      var attrNameRegex = /\[(\d+)\]/g;
      var attrNameValue = '[' + counter + ']';
      var jsonText      = $($repeater.data().jsonId).text();

      if (!jsonText.length) {
        return;
      }

      var properties = $.parseJSON(jsonText);

      for (var i = 0, l = properties.length; i < l; i++) {
        properties[i].slug.replace(attrNameRegex, attrNameValue);
      }

      $.ajax({
        type: 'POST',
        data: JSON.stringify(properties),
        url: papi.ajaxUrl + '?papi-ajax=get_properties',
        dataType: 'json'
      }).success(function(res) {
        var html = [
          '<tr>',
            '<td class="handle"><span>' + (counter + 1) + '</span></td>'
        ];

        for (var i = 0, l = res.length; i < l; i++) {
          html.push('<td>' + res[i] + '</td>');
        }

        html.push('<td class="last">');
          html.push('<span>');
            html.push('<a title="' + papiL10n.remove + '" href="#" class="repeater-remove-item">x</a>');
          html.push('</span>');
        html.push('</td>');

        var $template = $(html.join('') + '</tr>');

        // Update array number in html name and name if ends with a number.
        $template.find('[name],[data-slug], [data-id]').each(function () {
          var $this = $(this);
          var attr  = '';
          var value = '';
          var attrs = [
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
          ];

          for (var i = 0, l = attrs.length; i < l; i++) {
            if ($this.attr(attrs[i].source) !== undefined) {
              attr  = attrs[i].target;
              value = $this.attr(attrs[i].source);
            }
          }

          value = value.replace(attrNameRegex, attrNameValue);

          $this.attr(attr, value);
        });

        $template
          .appendTo($table);

        // Trigger the property that we just added
        $template
          .find('[name*="_property"]')
          .trigger('papi/property/repeater/added');

        $('html, body')
          .animate({
            scrollTop: $('> tr:last', $table).offset().top
          });

        $table
          .closest('.papi-property-repeater')
          .find('.papi-property-repeater-rows')
          .val($table.find('tr').length);
      });
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

        $this.find('input, select, textarea').each(function () {
          $this = $(this);

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

})(window.jQuery, window.papiL10n);
