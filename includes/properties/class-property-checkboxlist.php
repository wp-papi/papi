<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property CheckboxList
 */

class PropertyCheckboxList extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    $html = '';

    $custom = $this->get_custom_options(array(
      'checkboxes' => array(),
      'selected'   => array()
    ));
    
    $checkboxes = $custom->checkboxes;
    $selected = $custom->selected;
    
    if (!is_null($this->get_options()->value) && !empty($this->get_options()->value)) {
      $selected = $this->get_options()->value;
    }
    
    if (!is_array($selected)) {
      $selected = array($selected);
    }
    
    foreach ($checkboxes as $key => $value) {
      $attributes = array(
        'value' => $key,
        'name' => $this->get_options()->name . '[]'
      );
      
      if (in_array($key, $selected)) {
        $attributes['checked'] = 'checked';
      }
      
      $html .= PTB_Html::input('checkbox', $attributes);
      $html .= $value;
      $html .= '<br />';
    }
    
    return $html;
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
   *
   * @return string
   */

  public function convert ($value) {
    return is_array($value) ? $value : array();
  }
  
}