<?php

/**
 * Page Type Builder Properties Base class.
 */

class PTB_Properties_Base {

  /**
   * Generate HTML from a property type with tr and td tags.
   *
   * @param object $property Property object
   * @param array $args Array of attributes
   * @param string $inner Inner html
   * @since 1.0
   *
   * @return string
   */

  public function toHTML ($property, $args = array(), $inner = '') {
    // Some properties has special html output.
    $special_property = $this->special_properties($property);
    if (!is_null($special_property) && !empty($special_property)) {
      return $special_property;
    }

    // Procced with the normal html output.
    $attributes = '';
    foreach ($args as $key => $value) {
      $attributes .= ' ' . $key . '="' . $value . '" ';
    }
    $html = ptb_get_html_for_type($property->type);
    $html = str_replace('{{attributes}}', $attributes, $html);
    $html = str_replace('{{inner}}', $inner, $html);
    $html = PTB_Html::td($html);
    $label = PTB_Html::td(PTB_Html::label($property->title, $property->key));
    return PTB_Html::tr($label . $html);
  }

}