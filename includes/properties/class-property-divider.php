<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Divider
 */

class PropertyDivider extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return '<h3 class="hndle"><span>' . $this->get_options()->title . '</span></h3>';
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    $html = PTB_Html::td($this->html());
    return PTB_Html::tr($html);
  }

}