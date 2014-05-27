<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Date
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyDate extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Database value.
    $value = $this->get_value('');

    if (is_string($value)) {
      $value = $this->convert($value);
    }

    if ($value instanceof DateTime) {
      $value = $value->format('Y-m-d');
    }

    echo PTB_Html::input('text', array(
      'name' => $options->slug,
      'value' => $value,
      'class' => $this->css_classes(),
      'data-ptb-property' => 'date'
    ));
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return array|string
   */

  public function convert ($value) {
    if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value)) {
      return new DateTime($value);
    }

    return $value;
  }

  /**
   * Output custom JavaScript for the property.
   *
   * @since 1.0
   */

  public function js () {
    $files = array('moment.min.js', 'pikaday.min.js', 'pikaday.jquery.min.js');
    foreach ($files as $file) {
      wp_enqueue_script($file, $this->js_url . 'vendors/' . $file, array(), '1.0.0', true);
    }
  }

}