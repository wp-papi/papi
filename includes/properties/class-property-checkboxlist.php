<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property CheckboxList
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyCheckboxList extends PTB_Property {

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
      'selected' => array()
    ));

    // Override selected setting with
    // database value if not null.
    if (!is_null($value)) {
      $settings->selected = $value;
    }

    // Selected setting need to be an array.
    if (!is_array($settings->selected)) {
      $settings->selected = array($settings->selected);
    }

    foreach ($settings->items as $key => $value) {

      if (is_numeric($key)) {
        $key = $value;
      }

      ?>
      <input type="checkbox" value="<?php echo $key; ?>" name="<?php echo $options->slug; ?>[]" <?php echo in_array($key, $settings->selected) ? 'checked="checked"': ''; ?> />
      <?php
      echo $value . '<br />';
    }
  }

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param mixed $value
   * @since 1.0
   *
   * @return string
   */

  public function format_value ($value) {
    if (is_string($value)) {
      return array($value);
    }

    if (is_array($value)) {
      return $value;
    }

    return null;
  }

}