import $ from 'jquery';

/**
 * Property Repeater.
 */

class Repeater {

  /**
   * The template to use.
   *
   * @var {function}
   */

  get template() {
    return window.wp.template('papi-property-repeater-row');
  }

  /**
   * Initialize Property Repeater.
   */

  static init() {
    new Repeater().binds();
  }

  /**
   * Prepare to add a new row to the repeater
   * and then call fetch to fetch Papi ajax data.
   *
   * @param {object} $this
   */

  add($this) {
    const $repeater = $this.closest('.papi-property-repeater-top');
    const slug      = $repeater.data().slug;
    const $tbody    = $repeater.find('.repeater-tbody');
    const counter   = $tbody.children().length;
    const jsonText  = this.getJSON($this);

    if (!jsonText.length) {
      return;
    }

    let properties = this.prepareProperties(jsonText, counter);
    const self = this;

    this.fetch(properties, function (res) {
      self.addRow($tbody, counter, res);
    });
  }

  /**
   * Add a new row to the repeater.
   *
   * @param {object} $tbody
   * @param {int} counter
   * @param {array} items
   */

  addRow($tbody, counter, res) {
    let columns = [];

    for (var i = 0, l = res.html.length; i < l; i++) {
      columns.push('<td>' + res.html[i] + '</td>');
    }

    let $row = this.getHtml({
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
        const $tbody = $(this).closest('.repeater-tbody');
        self.updateRowNumber($tbody);
      }
    });

    $(document).on('click', '.papi-property-repeater .bottom a.button', function (e) {
      e.preventDefault();
      self.add($(this));
    });

    $(document).on('click', '.papi-property-repeater .repeater-remove-item', function (e) {
      e.preventDefault();
      self.remove($(this));
    });
  }

  /**
   * Fetch properties from Papi ajax.
   *
   * @param {array} properties
   * @param {function} callback
   */

  fetch(properties, callback) {
    $.ajax({
      type: 'POST',
      data: JSON.stringify(properties),
      url: papi.ajaxUrl + '?action=get_properties',
      dataType: 'json'
    }).success(callback);
  }

  /**
   * Get JSON properties template.
   *
   * @param {object} $this
   *
   * @return {string}
   */

  getJSON($this) {
    return $('script[data-papi-json="' + $this.data().papiJson + '"]').first().text();
  }

  /**
   * Get row html as jQuery object.
   *
   * @param {object} data
   *
   * @return {object}
   */

  getHtml(data) {
    let template = this.template;
    template = window._.template(template());
    return $(template(data));
  }

  /**
   * Prepare properties.
   *
   * @param {array} properties
   * @param {int} counter
   */

  prepareProperties(properties, counter) {
    const attrNameRegex = /\[(\d+)\]/g;

    if (typeof properties === 'string') {
      properties = $.parseJSON(properties);
    }

    for (let i = 0, l = properties.length; i < l; i++) {
      properties[i].slug = properties[i].slug.replace(attrNameRegex, '[' + counter + ']');
    }

    return properties;
  }

  /**
   * Remove item from the repeater.
   *
   * @param {object} e
   */

  remove($this) {
    const $tbody = $this.closest('.papi-property-repeater-top').find('.repeater-tbody');

    $this.closest('tr').remove();

    this.updateRowNumber($tbody);
  }

  /**
   * Scroll down to table.
   *
   * @param {object} $tbody
   */

  scrollDownTable($tbody) {
    $('html, body')
      .animate({
        scrollTop: $('> tr:last', $tbody).offset().top
      });
  }

  /**
   * Update table row number.
   *
   * @param {object} $tbody
   */

  updateRowNumber($tbody) {
    $tbody.find('> tr').each((i, el) => {
      let $el = $(el);

      $el.find('> td:first-child span').text(i + 1);

      $el.find('[name*="papi_"]').each(function () {
        let $input = $(this);

        $input.attr('name', $input.attr('name').replace(/(\[\d+\])/, '[' + i + ']'));
      });
    });

    this.updateDatabaseRowNumber($tbody);
  }

  /**
   * Update database row number.
   *
   * @param {object} $el
   */

  updateDatabaseRowNumber($tbody) {
    $tbody
      .closest('.papi-property-repeater-top')
      .find('.papi-property-repeater-rows')
      .val($tbody.find('tr').length);
  }

}

export default Repeater;
