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
          html          = $template.html(),
          dataNameRegex = /data\-name\=/g,
          attrNameRegex = /name\=\"\papi\_\w+(\[\d+\])\[(\w+)\]\"/g,
          attrNameValue = '[' + counter + ']';

      html = html.replace(dataNameRegex, 'name=');

      // Update array number in html name and name if ends with a number.
      html = html.replace(attrNameRegex, function (match, num) {
        return match.replace(num, attrNameValue);
      });

      $template.html(html).find('td:first-child span').text(counter + 1);

      $template.appendTo($table);

      $('html, body').animate({
        scrollTop: $('> tr:last', $table).offset().top
      });
    },

    /**
     * Initialize the repeater.
     *
     * @param {object} $this
     */

    init: function ($this) {
      var self = this;

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
          $(this).attr('name', $(this).attr('name').replace(/(\[\d+\])/, '[' + index + ']'));
        });
      });
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

  })(jQuery);
