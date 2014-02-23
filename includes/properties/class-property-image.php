<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Image
 */

class PropertyImage extends PTB_Property {

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
    
    $html = PTB_Html::tag('img', array(
      'src' => $this->get_options()->value,
      'class' => 'ptb-property-image ' . $css_class,
      'data-ptb-property' => 'image'
    ));
    
    return $html . PTB_Html::input('hidden', array(
      'value' => $this->get_options()->value,
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name
    ));
  }

}