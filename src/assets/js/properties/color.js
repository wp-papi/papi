import $ from 'jquery';

/**
 * Property Color.
 *
 * Uses the build in color picker in WordPress.
 */
class Color {

  /**
   * Initialize Property Color.
   */
  static init() {
    new Color().binds();
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    let self = this;

    $('.papi-property-color-picker input').each(self.showColorPicker);

    // Bind all new inputs when added in repeater.
    $(document).on('papi/property/repeater/added', '[data-property="color"]', function () {
      $('.papi-property-color-picker input').each(self.showColorPicker);
    });
  }

  /**
   * Show color picker.
   */
  showColorPicker() {
    const $el      = $(this);
    const palettes = $el.data().palettes;

    if (!$el.parent().hasClass('papi-property-color-picker')) {
      return;
    }

    $el.wpColorPicker({
      color: true,
      palettes: palettes === undefined || !palettes.length ? false : palettes
    });
  }
}

export default Color;
