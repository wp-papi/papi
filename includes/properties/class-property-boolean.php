<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Boolean
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyBoolean extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    echo PTB_Html::input('checkbox', array(
      'name' => $this->get_options()->slug,
      'checked' => $this->get_options()->value ? 'checked' : '',
      'class' => $this->css_classes()
    ));
  }

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0.0
   *
   * @return boolean
   */

  public function format_value ($value) {
    return isset($value) && !empty($value);
  }

}