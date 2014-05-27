<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Number
 */

class PropertyNumber extends PTB_Property {

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

    echo PTB_Html::input('number', array(
      'name'  => $options->slug,
      'value' => $value,
      'class' => $this->css_classes()
    ));
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return int|float
   */

  public function convert ($value) {
    if (floatval($value) && intval($value) != floatval($value)) {
      return floatval($value);
    } else {
      return intval($value);
    }
  }
}