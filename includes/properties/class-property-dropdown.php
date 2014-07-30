<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Dropdown
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyDropdown extends PTB_Property {

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
    if (!is_null($value)) {
      $settings->selected = $value;
    }

    ?>
    <select class="ptb-property-dropdown" name="<?php echo $options->slug; ?>" class="<?php echo $this->css_classes(); ?>">
      <?php
        foreach ($settings->items as $key => $value):
          if (is_numeric($key)) {
            $key = $value;
          }
      ?>
        <option value="<?php echo $key; ?>" <?php echo $key == $settings->selected ? 'selected="selected"' : ''; ?>><?php echo $value; ?></option>
      <?php endforeach; ?>
    </select>
    <?php
  }

}