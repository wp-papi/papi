<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Papi - Property Radio
 *
 * @package Papi
 * @version 1.0.0
 */

class PropertyRadio extends Papi_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Database value. Can be null.
    $value = $this->get_value();

    // Property settings from the page type.
    $settings = $this->get_settings(array(
      'items'    => array(),
      'selected' => ''
    ));

    // Override selected setting with
    // database value if not null.
    if (!empty($value)) {
      $settings->selected = $value;
    }

    foreach ($settings->items as $key => $value) {

      if (is_numeric($key)) {
        $key = $value;
      }

      ?>
      <input type="radio" value="<?php echo $key ?>" name="<?php echo $options->slug; ?>" <?php echo $key == $settings->selected ? 'checked="checked"' : ''; ?> />
      <?php
      echo $value . '<br />';
    }
  }

}
