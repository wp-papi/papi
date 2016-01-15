import $ from 'jquery/jquery';

/**
 * Property Term.
 *
 * Using Select2.
 */
class Term {

  /**
   * Initialize Property Term.
   */
  static init() {
    new Term().binds();
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $(document).on('papi/property/repeater/added', '[data-property="term"]', this.update);
    $(document).on('change', '.papi-property-term-left', this.change);
  }

  /**
   * Change term dropdown when selecting
   * a different taxonomy.
   *
   * @param {object} e
   */
  change(e) {
    e.preventDefault();

    const $this = $(this);
    const query = $this.data('term-query').length
      ? $this.data('term-query')
      : {};

    const params = {
      'action': 'get_terms',
      'taxonomy': $this.val(),
      'query': query
    };
    const $prop = $this.closest('.papi-property-term');
    const $select = $prop.find('.papi-property-term-right');

    $('[for="' + $select.attr('id') + '"]')
      .parent()
      .find('label')
      .text($this.data('select-item').replace('%s', $this.find('option:selected').text()));

    $.get(papi.ajaxUrl + '?' + $.param(params), function(terms) {
      $select.empty();

      $.each(terms, function(term_id, term_name) {
        $select
          .append($('<option></option>')
          .attr('value', term_id)
          .text(term_name));
      });

      if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
        $select.select2();
      }
    });
  }

  /**
   * Initialize pikaday field when added to repeater.
   *
   * @param {object} e
   */
  update(e) {
    e.preventDefault();

    const $select = $(this).parent().find('select');

    if ($select.hasClass('papi-component-select2') && 'select2' in $.fn) {
      $select.select2();
    }
  }

}

export default Term;
