import $ from 'jquery';
import select2Options from 'components/select2';

/**
 * Property Module.
 *
 * Using Select2.
 */
class Module {

  /**
   * The option template to compile.
   *
   * @return {function}
   */
  get optionTemplate() {
    return window.wp.template('papi-property-module-option');
  }

  /**
   * The option placeholder template to compile.
   *
   * @return {function}
   */
  get optionPlaceholderTemplate() {
    return window.wp.template('papi-property-module-option-placeholder');
  }

  /**
   * Initialize Property Module.
   */
  static init() {
    new Module().binds();
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $(document).on('papi/property/repeater/added', '[data-property="post"]', this.update.bind(this));
    $(document).on('change', '.papi-property-module-right', this.change.bind(this));
    $(document).on('papi/iframe/submit', this.iframeSubmit.bind(this));
  }

  /**
   * Change template dropdown when selecting
   * a different module.
   *
   * @param {object} e
   */
  change(e) {
    e.preventDefault();

    const self = this;
    const $this = $(e.currentTarget);

    const entryTypeId = $this.find('option:selected').data('entry-type');
    const params  = {
      'action': 'get_entry_type',
      'entry_type': entryTypeId
    };
    const $prop   = $this.closest('.papi-property-module');
    const $select = $prop.find('.papi-property-module-left');

    $.get(papi.ajaxUrl + '?' + $.param(params), function(entryType) {
      $select.empty();

      if ($select.data('placeholder') && posts.length) {
        const optionPlaceholderTemplate = self.optionPlaceholderTemplate;
        const template1 = window._.template($.trim(optionPlaceholderTemplate()));

        $select.append(template1());
      }

      const optionTemplate = self.optionTemplate;
      const template2 = window._.template($.trim(optionTemplate()));

      for (var key in entryType.template) {
        const template = entryType.template[key];
        let item = {};

        // Convert string value to item object.
        if (typeof template === 'string') {
          item = {
            'label': template,
            'template': template,
            'default': false
          };
        }

        // Check if template is a object
        // or bail.
        if ($.isPlainObject(template)) {
          item = template;
        } else {
          continue;
        }

        $select.append(template2({
          title: item.label,
          value: key
        }));

        if (item.default) {
          $select.val(key);
        }
      }

      if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
        $select.trigger('change');
      }
    });
  }

  /**
   * Update select when iframe is submitted.
   *
   * @param {object} e
   * @param {object} data
   */
  iframeSubmit(e, data) {
    if (!data.iframe) {
      return;
    }

    const $elm = $(data.iframe);
    const title = $elm.find('[name="post_title"]').val();
    const id = $elm.find('[name="post_ID"]').val();
    const $select = $('[name=' + data.selector + ']');

    if (data.url.indexOf('post-new') !== -1) {
      // new
      const optionTemplate = this.optionTemplate;
      const template = window._.template($.trim(optionTemplate()));

      if ($select.find('option[value=' + id + ']').length) {
        return;
      }

      $select.append(template({
        id: id,
        title: title,
      }));
    } else {
      // edit
      const $option = $select.find('option[data-edit-url="' + data.url + '"]');
      $option.removeData('data');
      $option.text(title);
    }

    $select.trigger('change');
    $select.val(id);
  }

  /**
   * Initialize select2 field when added to repeater.
   *
   * @param {object} e
   */
  update(e) {
    e.preventDefault();

    const $select = $(e.currentTarget).parent().find('select');

    if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
      $select.select2(select2Options($select[0]));
    }
  }
}

export default Module;
