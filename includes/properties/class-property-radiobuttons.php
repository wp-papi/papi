<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Radiolist
 */

class PropertyRadioButtons extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    $options = $this->get_options();
    $custom = $this->get_custom_options(array(
      'radiobuttons' => array(),
      'selected' => array()
    ));

    $value = '';

    if (!is_null($options->value) && !empty($options->value)) {
      $value = $options->value;
    }

    $html = '';

    foreach ($custom->radiobuttons as $key => $v) {
      $attributes = array(
        'value' => $v,
        'name' => $this->get_options()->name . '[]'
      );

      if ($v == $value) {
        $attributes['checked'] = 'checked';
      }

      $html .= PTB_Html::input('radio', $attributes);
      $html .= $key;
      $html .= '<br />';
    }

    return $html;
  }

  /**
   * Convert the value of the property before we output it to the application.
   *
   * @param mixed $values
   * @since 1.0
   *
   * @return mixed
   */

  public function convert ($values) {
    if (is_array($values)) {
      return reset($values);
    }

    return null;
  }

}