<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property String
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyString extends PTB_Property {

  /**
   * The input type to use.
   *
   * @var string
   * @since 1.0.0
   */

  public $input_type = 'text';

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

    ?>
    <input type="<?php echo $this->input_type; ?>" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="<?php echo $this->css_classes(); ?>" />
    <?php
  }

}