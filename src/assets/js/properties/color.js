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
    const settings = $el.data().settings || {};

    if (!$el.parent().hasClass('papi-property-color-picker')) {
      return;
    }

    if (typeof settings.default_color !== 'undefined') {
      settings.defaultColor = settings.default_color;
      delete settings.default_color;
    }

    if (typeof settings.palettes !== 'undefined') {
      settings.palettes = $.isArray(settings.palettes) && settings.palettes.length ? settings.palettes : true;
    }

    $el.wpColorPicker(settings);
  }
}

export default Color;
