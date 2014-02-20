<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Date
 */

class PropertyDate extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return PTB_Html::input('date', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'value' => $this->get_options()->value
    ));
  }

}