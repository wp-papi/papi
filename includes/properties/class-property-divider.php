<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Act - Property Divider
 *
 * @package Act
 * @version 1.0.0
 */

class PropertyDivider extends Act_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options
    $options = $this->get_options();
    ?>
      <h3 class="hndle act-property-divider">
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
    ?>
      <tr class="act-fullwidth">
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
