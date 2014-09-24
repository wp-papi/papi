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

    ?>
    <input type="text" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="<?php echo $this->css_classes(); ?>" data-papi-property="date" />
    <?php
  }

}
