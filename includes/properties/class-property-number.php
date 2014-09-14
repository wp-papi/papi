<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Number
 */

class PropertyNumber extends PropertyString {

  /**
   * The input type to use.
   *
   * @var string
   * @since 1.0.0
   */

  public $input_type = 'number';

  /**
   * The default value.
   *
   * @var string
   * @since 1.0.0
   */

  public $default_value = 0;

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return array
   */

  public function format_value ($value, $post_id) {
    if (floatval($value) && intval($value) != floatval($value)) {
      return floatval($value);
    } else {
      return intval($value);
    }
  }
}