<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Number
 */

class PropertyNumber extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Database value.
    $value = $this->get_value(0);
    ?>
    <input type="number" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="<?php echo $this->css_classes(); ?>" />
    <?php
  }

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