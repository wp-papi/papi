<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Date
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyDate extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Database value.
    $value = $this->get_value('');

    if (is_string($value)) {
      $value = $this->format_value($value, 0);
    }

    if ($value instanceof DateTime) {
      $value = $value->format('Y-m-d');
    }

    ?>
    <input type="text" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="<?php echo $this->css_classes('ptb-property-date'); ?>" />
    <?php
  }

  /**
   * Format the value of the property before we output it to the application.
   *
   * @param string $value
   * @param int $post_id
   * @since 1.0.0
   *
   * @return array
   */

  public function format_value ($value, $post_id) {
    if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value)) {
      return new DateTime($value);
    }

    return $value;
  }

}