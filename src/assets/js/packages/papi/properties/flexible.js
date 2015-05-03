import $ from 'jquery';
import Repeater from 'papi/properties/repeater';

class Flexible extends Repeater {

  /**
   * The template to use.
   *
   * @var {function}
   */

  get template() {
    return window.wp.template('papi-property-flexible-row');
  }

  /**
   * Initialize Property Flexible.
   */

  static init() {
    new Flexible().binds();
  }

  /**
   * Add a new row to the flexible repeater.
   *
   * @param {object} $tbody
   * @param {int} counter
   * @param {array} items
   */

  addRow($tbody, counter, res) {
    var columns = [];

    for (let i = 0, l = res.html.length; i < l; i++) {
      let layoutSlug = this.properties[i].slug.substring(0, this.properties[i].slug.length - 1) + '_layout]';
      if (i === l - 1) {
        columns.push('<td class="flexible-td-last">');
      } else {
        columns.push('<td>');
      }
      columns.push('<input type="hidden" name="' +  layoutSlug + '" value="' + this.currentLayout + '" />');
      columns.push(res.html[i] + '</td>');
    }

    var $row = this.getHtml({
      columns: columns.join(''),
      counter: counter
    });

    $row.appendTo($tbody);

    // Trigger the property that we just added
    console.log($row
      .find('[name*="_property"]').attr('data-property'));
    $row
      .find('[name*="_property"]')
      .trigger('papi/property/repeater/added');

    this.scrollDownTable($tbody);
    this.updateDatabaseRowNumber($tbody);
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    const self = this;

    $('.repeater-tbody').sortable({
      revert: true,
      handle: '.handle',
      helper: function (e, ui) {
        ui.children().each(function() {
          $(this).width($(this).width());
        });
        return ui;
      },
      stop: function () {
        self.updateRowNumber($(this).closest('.repeater-tbody'));
      }
    });

    $(document).on('click', '.papi-property-flexible .bottom a.button', function (e) {
      e.preventDefault();
      self.add($(this));
    });

    $(document).on('click', '.papi-property-flexible .repeater-remove-item', function (e) {
      e.preventDefault();
      self.remove($(this));
    });
  }

  /**
   * Prepare properties.
   *
   * @param {array} properties
   * @param {int} counter
   */

  prepareProperties(jsonText, counter) {
    const properties   = $.parseJSON(jsonText);
    this.currentLayout = properties.layout;
    this.properties    = super.prepareProperties(properties.properties, counter);
    return this.properties;
  }

  /**
   * Remove item from the flexible repeater.
   *
   * @param {object} e
   */

  remove($this) {
    const $tbody = $this.closest('.papi-property-flexible').find('.repeater-tbody');

    $this.closest('tr').remove();

    this.updateRowNumber($tbody);
  }

}

export default Flexible;
