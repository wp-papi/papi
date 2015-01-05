(function ($) {

  // Property color

  papi.properties.color = {

    /**
     * Initialize property color.
     */

    init: function () {
      $('.papi-color-picker').each(function (i, el) {
        var $el = $(el);

        $el.wpColorPicker({
          color: true,
          palettes: $el.data('palettes') === undefined ? false : $el.data('palettes')
        });
      });
    }
  };

})(jQuery);
