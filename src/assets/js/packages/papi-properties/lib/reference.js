import $ from 'jquery';

/**
 * Property Reference.
 */

class Reference {

  /**
   * Initialize Property Reference.
   */

  static init() {
    new Reference().binds();
  }

  /**
   * Bind elements with functions.
   */

  binds() {
    $(document).on('click', '.papi-property-reference .handlediv', this.toggle);
  }

  /**
   * Toggle page type div.
   *
   * @parma {object} e
   */

  toggle(e) {
    e.preventDefault();

    $(this).parent()
      .toggleClass('closed')
      .next()
      .toggle();
  }

}

export default Reference;
