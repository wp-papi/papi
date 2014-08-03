<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Divider
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyDivider extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options
    $options = $this->get_options();
    ?>
      <h3 class="hndle ptb-property-divider">
        <span><?php echo $options->title; ?></span>
      </h3>
    <?php
  }

  /**
   * Render the final html that is displayed in a table.
   *
   * @since 1.0.0
   */

  public function render () {
    $options = $this->get_options();
    ?>
      <tr class="ptb-fullwidth">
        <td colspan="2">
          <?php
            $this->html();
            $this->helptext();
          ?>
        </td>
      </tr>
    <?php
  }

}