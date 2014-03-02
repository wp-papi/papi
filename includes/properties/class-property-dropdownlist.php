<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property DropdownList
 */

class PropertyDropdownList extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    $html = PTB_Html::tag('select', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'class' => $this->css_classes()
    ), false);
    
    $options = isset($this->get_options()->custom->options) ? $this->get_options()->custom->options : array();
    $selected = isset($this->get_options()->custom->selected) ? $this->get_options()->custom->selected : '';
    
    if (!is_null($this->get_options()->value) && !empty($this->get_options()->value)) {
      $selected = $this->get_options()->value;
    }
    
    foreach ($options as $key => $value) {
      $attributes = array(
        'value' => $key
      );
      
      if ($key == $selected) {
        $attributes['selected'] = 'selected';
      }
      
      $html .= PTB_Html::tag('option', $value, $attributes);
    }
    
    return $html . PTB_Html::stop('select');
  }
  
}