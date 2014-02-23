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
    if (isset($this->get_options()->custom->css_class)) {
      $css_class = $this->get_options()->custom->css_class;
    } else {
      $css_class = '';
    }
    
    return PTB_Html::input('checkbox', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'selected' => empty($this->get_options()->value) ? '' : 'selected',
      'class' => $css_class
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