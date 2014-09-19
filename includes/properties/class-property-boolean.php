<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Act - Property Boolean
 *
 * @package Act
 * @version 1.0.0
 */

class PropertyBoolean extends Act_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    echo Act_Html::input('checkbox', array(
      'name' => $this->get_options()->slug,
      'checked' => $this->get_options()->value ? 'checked' : '',
      'class' => $this->css_classes()
    ));
  }

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return boolean
   */

  public function format_value ($value, $post_id) {
    return isset($value) && !empty($value);
  }

}
