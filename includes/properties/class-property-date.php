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
    if (isset($this->get_options()->custom->css_class)) {
      $css_class = $this->get_options()->custom->css_class;
    } else {
      $css_class = '';
    }
    
    $value = !empty($this->get_options()->value) ? $this->get_options()->value->format('Y-m-d') : '';
    
    return PTB_Html::input('text', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'value' => $value,
      'class' => $css_class,
      'data-ptb-date' => true
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
    $file = 'moment.min.js';
    wp_enqueue_script($file, $this->js_url . 'vendors/' . $file, array(), '1.0.0', true);
    $file = 'pickaday.min.js';
    wp_enqueue_script($file, $this->js_url . 'vendors/' . $file, array(), '1.0.0', true);
    $file = 'pickaday.jquery.min.js';
    wp_enqueue_script($file, $this->js_url . 'vendors/' . $file, array(), '1.0.0', true);
  }

}