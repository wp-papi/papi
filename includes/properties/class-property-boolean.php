<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Boolean
 */

class PropertyBoolean extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return PTB_Html::input('checkbox', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'selected' => empty($this->get_options()->value) ? '' : 'selected',
      'class' => $this->css_classes()
    ));
  }
  
  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
   *
   * @return boolean
   */
  
  public function convert ($value) {
    return isset($value) && !empty($value);
  }

}