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
    if (isset($this->get_options()->value) && is_numeric($this->get_options()->value)) {
      $value = wp_get_attachment_url($this->get_options()->value);
    } else {
      $value = '';
    }
    
    $html = PTB_Html::tag('img', array(
      'src' => $value,
      'class' => $this->css_classes('ptb-property-image'),
      'data-ptb-property' => 'image'
    ));
    
    return $html . PTB_Html::input('hidden', array(
      'value' => $this->get_options()->value,
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name
    ));
  }
  
  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
   *
   * @return object|string
   */
  
  public function convert ($value) {
    if (is_numeric($value)) {
      $meta = wp_get_attachment_metadata($value);
      if (isset($meta) && !empty($meta)) {
        $mine = array(
          'is_image' => true,
          'url' => wp_get_attachment_url($value)
        );
        return (object)array_merge($meta, $mine);
      } else {
        return $value;
      }
    } else {
      return $value;
    }
  }
  
  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    $label = PTB_Html::td($this->label(), array('colspan' => 2));
    $label = PTB_Html::tr($label);
    $html = PTB_Html::td($this->html(), array('colspan' => 2));
    $html = PTB_Html::tr($html);
    $html .= $this->helptext(false);
    return $label . $html;
  }

}