<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Papi - Property Divider
 *
 * @package Papi
 * @version 1.0.0
 */

class PropertyDivider extends Papi_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options
    $options = $this->get_options();
    ?>
      <h3 class="hndle papi-property-divider">
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
      <tr class="papi-fullwidth">
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
