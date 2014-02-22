<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Date
 */

class PropertyDate extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return PTB_Html::input('date', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'value' => $this->get_options()->value
    ));
  }
  
  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
   *
   * @return array|string
   */
  
  public function convert ($value) {
    if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value)) {
      return date_parse($value);
    }
    return $value;
  }

}