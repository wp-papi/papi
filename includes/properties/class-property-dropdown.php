<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Papi - Property Dropdown
 *
 * @package Papi
 * @version 1.0.0
 */

class PropertyDropdown extends Papi_Property {

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
    // database value if not empty.
    if (!empty($value)) {
      $settings->selected = $value;
    }

    ?>
    <select class="papi-property-dropdown" name="<?php echo $options->slug; ?>" class="<?php echo $this->css_classes(); ?>">
      <?php
        foreach ($settings->items as $key => $value):
          if (is_numeric($key)) {
            $key = $value;
          }
      ?>
        <option value="<?php echo $value; ?>" <?php echo $value == $settings->selected ? 'selected="selected"' : ''; ?>><?php echo $key; ?></option>
      <?php endforeach; ?>
    </select>
    <?php
  }

}
