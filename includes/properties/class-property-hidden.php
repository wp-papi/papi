<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Hidden
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyHidden extends PTB_Property {

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
    <input type="hidden" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="<?php echo $this->css_classes(); ?>" />
    <?php
  }

}