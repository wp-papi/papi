import $ from 'jquery';

/**
 * Property Dropdown.
 *
 * Using Select2.
 */

class Dropdown {

  /**
   * Initialize Property Color.
   */

  static init() {
    new Dropdown().binds();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $(document).on('papi/property/repeater/added', '[value="dropdown"]', this.update);
  }

  /**
   * Update select if isn't a select2.
   */

  update(e) {
    e.preventDefault();

    const $select = $(this).parent().find('select');

    if ($select.hasClass('papi-vendor-select2') && typeof $select.select2 === 'function') {
      $select.select2();
    }
  }

}

export default Dropdown;
