import $ from 'jquery';
import Pikaday from 'components/pikaday';

/**
 * Property Datetime.
 *
 * Using Pikaday with time fields and some custom fixes.
 */
class Datetime {

  /**
   * Initialize Property Datetime.
   */
  static init() {
    new Datetime().binds();
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    this.pikaday($('.inside > .papi-table > tbody > tr > td > input.papi-property-datetime'));
    this.pikaday($('.papi-table .papi-table input.papi-property-datetime'));

    $(document).on('papi/property/repeater/added', '[data-property="datetime"]', this.updateSelect.bind(this));
  }

  /**
   * Initialize Pikaday.
   *
   * @param {object} $prop
   */
  pikaday($props) {
    if (!$props.length) {
      return;
    }

    $props.each(function () {
      let $prop = $(this);
      let settings = $prop.data().settings;

      // Expects settings to be defined.
      if (typeof settings === 'undefined') {
        return;
      }

      // Fixes to 24 hours actually works if you forget to change the format.
      if (settings.use24hour) {
        settings.format = settings.format.replace(/hh/, 'HH');
      }

      settings.field = $prop[0];

      /* eslint-disable */
      new Pikaday(settings);
      /* eslint-enable */
    });
  }

  /**
   * Initialize pikaday field when added to repeater.
   *
   * @param {object} e
   */
  updateSelect(e) {
    e.preventDefault();
    this.pikaday($(e.currentTarget).prev().find('.papi-property-datetime'));
  }
}

export default Datetime;
