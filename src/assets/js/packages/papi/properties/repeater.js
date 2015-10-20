import $ from 'jquery/jquery';

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
    const $tbody    = $repeater.find('.repeater-tbody');
    const counter   = $tbody.children().length;
    const jsonText  = this.getJSON($this);
    const limit     = $repeater.data().limit;
    const append    = limit === undefined || limit === -1 || $tbody.find('> tr').length < limit;

    if (!jsonText.length || !append) {
      return;
    }

    let properties = $.parseJSON(jsonText);

    const self = this;
    this.fetch(properties, counter, function (res) {
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
      if (typeof res.html[i] === 'string') {
        columns.push(res.html[i]);
      }
    }

    let $row = this.getHtml({
      columns: columns.join(''),
      counter: counter
    });

    $row.appendTo($tbody);
    $row.find('[name*="_property"]').trigger('papi/property/repeater/added');
    $row.find('[data-papi-rules="true"]').trigger('init');

    this.scrollDownTable($tbody);
    this.updateDatabaseRowNumber($tbody);

    $tbody.closest('.papi-property-repeater-top').trigger('change');
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

    $(document).on('click', '.papi-property-repeater .bottom button[type=button]', function (e) {
      e.preventDefault();
      self.add($(this));
    });

    $(document).on('click', '.repeater-tbody span.toggle', function (e) {
      e.preventDefault();
      // e.stopPropagation();
      self.toggle($(this));
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
   * @param {int} counter
   * @param {function} callback
   */
  fetch(properties, counter, callback) {
    const params = {
      'action': 'get_properties',
      'counter': counter,
      'post_type': $('#post_type').val()
    };
    $.ajax({
      type:     'POST',
      data:     {
        properties: JSON.stringify(properties)
      },
      url:      papi.ajaxUrl + '?' + $.param(params),
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
    template = window._.template($.trim(template()));
    return $(template(data));
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
    const $tr = $('> tr:last', $tbody);
    $('html, body').animate({
      scrollTop: $tr.offset().top - $tr.height()
    });
  }

  /**
   * Toggle the row content.
   *
   * @param {object} $this
   */
  toggle($this) {
    $this.closest('tr').toggleClass('closed');
  }

  /**
   * Trigger conditional rule.
   *
   * @param {object} $prop
   */
  triggerRule($tbody, counter) {
    const $top  = $tbody.closest('.papi-property-repeater-top');
    let name    = $top.find('.bottom').next().attr('name').replace('[]', '');
    $('[data-papi-rule="' + name + '"]').data('papi-rule-value', counter).trigger('change');
  }

  /**
   * Update table row number.
   *
   * @param {object} $tbody
   */
  updateRowNumber($tbody) {
    $tbody.find('> tr').each((i, el) => {
      let $el = $(el);

      $el.find('> td:first-child .count').text(i + 1);

      $el.find('[data-replace-slug="true"]').each(function () {
        let $prop = $(this);
        $prop.attr('data-slug', $prop.attr('data-slug').replace(/(\[\d+\])/, '[' + i + ']'));
      });

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
    let counter = $tbody.find('tr').length;

    $tbody
      .closest('.papi-property-repeater-top')
      .find('.papi-property-repeater-rows')
      .val();

    this.triggerRule($tbody, counter);
  }

}

export default Repeater;
