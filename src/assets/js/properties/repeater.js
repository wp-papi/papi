import $ from 'jquery';
import Utils from 'utils';

/* global tinymce, tinyMCEPreInit */

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
    const $tbody    = $repeater.find('.repeater-tbody').first();
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
      start: function (e, ui) {
        let editorIds = $.map($(ui.item).find('.wp-editor-area').get(), function(elem) {
          return elem.id;
        });
        self.deactivateEditors(editorIds);
      },
      stop: function (e, ui) {
        const $tbody = $(this).closest('.repeater-tbody');
        self.updateRowNumber($tbody);

        let editorIds = $.map($(ui.item).find('.wp-editor-area').get(), function(elem) {
          return elem.id;
        });
        self.activateEditors(editorIds);
      }
    });

    $(document).on('click', '.papi-property-repeater .bottom button[type="button"]', function (e) {
      e.preventDefault();
      self.add($(this));
    });

    $(document).on('click', '.repeater-tbody span.toggle', function (e) {
      e.preventDefault();
      self.toggle($(this));
    });

    $(document).on('click', '.papi-property-repeater .repeater-remove-item', function (e) {
      e.preventDefault();
      self.remove($(this));
    });
  }

  /**
   * Deactivate rich editors by instance ids.
   *
   * @param  {array} ids tinyMCE editor ids
   */
  deactivateEditors(ids) {
    $.each(ids, function() {
      const editor = tinymce.get(this);

      if (typeof this !== 'string' || !this.length || this[0] !== 'p') {
        return;
      }

      const $textarea = $('#' + this);
      let editorHeight;

      $textarea.data('papi-editor-reinit', !!editor);

      if (editor) {
        editorHeight = editor.isHidden() ? $textarea.height() : $(editor.getWin()).height();

        // wpautop is killing paragraphs on remove(SaveContent)
        // it will be reactivated on init anyway.
        editor.settings.wpautop = false;

        $textarea.outerHeight(editorHeight).data('papi-editor-html', editor.isHidden());
        editor.remove();
      }
    });
  }

  /**
   * Reactivate rich editors by instance ids.
   *
   * @param  {array} ids tinyMCE editor ids
   */
  activateEditors(ids) {
    $.each(ids, function() {
      if (typeof this !== 'string' || !this.length || this[0] !== 'p') {
        return;
      }

      const $textarea = $('#' + this);

      if (!tinymce.get(this) && $textarea.data('papi-editor-reinit')) {
        const init = tinymce.extend({}, tinyMCEPreInit.mceInit[this], { height: $textarea.outerHeight() });

        // don't reinit if editor is in text mode
        // wp will reinit it on mode switch
        if (!$textarea.data('papi-editor-html')) {
          tinymce.init(init);
        }
      }
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
      'meta_type': Utils.getMetaType()
    };

    params[Utils.getMetaTypeKey()] = Utils.getMetaTypeValue();

    $.ajax({
      type: 'POST',
      data: {
        properties: JSON.stringify(properties)
      },
      url: papi.ajaxUrl + '?' + $.param(params),
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
   * Replace array number in name array.
   *
   * @param  {string} name
   * @param  {int} j
   * @param  {int} p
   *
   * @return {string}
   */
  replaceArrayNumber(name, j, p) {
    name = name.split('[');

    if (typeof p === 'undefined') {
      p = 0;
    }

    for (var i = 0, l = name.length; i < l; i++) {
      var part = name[i];

      if (/\d+$/.test(part.replace(']', '')) && (p === 0 || p === i)) {
        name[i] = j + ']';
        break;
      }
    }

    return name.join('[');
  }

  /**
   * Remove item from the repeater.
   *
   * @param {object} e
   */
  remove($this) {
    let $tbody = $this.closest('.papi-property-repeater-top');

    if (!$tbody.hasClass('papi-property-flexible')) {
      $tbody = $tbody.find('.repeater-tbody').first();
      $this.closest('tr').remove();
      this.updateRowNumber($tbody);
    }
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
    const $top = $tbody.closest('.papi-property-repeater-top');
    const name = $top.find('.bottom').next().attr('name').replace('[]', '');
    $('[data-papi-rule="' + name + '"]').data('papi-rule-value', counter).trigger('change');
  }

  /**
   * Update table row number.
   *
   * @param {object} $tbody
   */
  updateRowNumber($tbody) {
    let self = this;

    $tbody.first().find('> tr').each((i, el) => {
      let $el = $(el);
      let $rpt = $el.closest('.papi-property-repeater-top');
      let position = 0;
      let count = 0

      if ($rpt.length) {
        while ($rpt.length) {
          if (!$rpt.parentsUntil('.papi-property-repeater-top').length) {
            break;
          }

          $rpt = $rpt.closest('.papi-property-repeater-top').prev();

          if ($rpt.length) {
            count++;

            if (count > 1) {
              position += 3;
            }
          }
        }
      }

      $el.find('> td:first-child .count').text(i + 1);

      // Replace `data-slug` attribute.
      $el.find('[data-replace-slug="true"]').each(function () {
        let $prop = $(this);
        $prop.attr('data-slug', self.replaceArrayNumber($prop.attr('data-slug'), i, position));
      });

      // Replace `data-papi-rule` attribute.
      $el.find('[data-papi-rule*="papi_"]').each(function () {
        let $prop = $(this);
        $prop.attr('data-papi-rule', self.replaceArrayNumber($prop.attr('data-papi-rule'), i, position));
      });

      // Replace `data-papi-json` attribute.
      $el.find('[data-papi-json*="papi_"]').each(function () {
        let $prop = $(this);
        $prop.attr('data-papi-json', self.replaceArrayNumber($prop.attr('data-papi-json'), i, position));
      });

      // Replace id attribute.
      $el.find('[id*="papi_"]').each(function () {
        let $prop = $(this);
        $prop.attr('id', self.replaceArrayNumber($prop.attr('id'), i, position));
      });

      // Replace name attribute.
      $el.find('[name*="papi_"]').each(function () {
        let $prop = $(this);
        $prop.attr('name', self.replaceArrayNumber($prop.attr('name'), i, position));
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
