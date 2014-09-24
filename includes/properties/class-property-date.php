<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Papi - Property Date
 *
 * @package Papi
 * @version 1.0.0
 */

class PropertyDate extends Papi_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Database value.
    $value = $this->get_value();
    $value = date('Y-m-d', $value);

    ?>
    <input type="text" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="<?php echo $this->css_classes(); ?>" data-papi-property="date" />
    <?php
  }

  /**
   * Convert value to integer.
   *
   * @param string $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return int
   */

  public function format_value ($value, $post_id) {
    return intval($value);
  }

  /**
   * Save the date as Unix timestamp.
   *
   * @param string $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return int
   */

  public function update_value ($value, $post_id) {
    return strtotime($value);
  }

}
