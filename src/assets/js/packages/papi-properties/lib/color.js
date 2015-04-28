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
    $('.papi-property-color-picker input').each(() => {
      const $el = $(this);
      const palettes = $el.data().palettes;

      $el.wpColorPicker({
        color: true,
        palettes: palettes === undefined ? false : palettes
      });
    });
  }

}

export default Color;
