<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Url
 */

class PropertyUrl extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return PTB_Html::input('url', array(
      'name' => $this->get_options()->name,
      'value' => $this->get_options()->value
    ));
  }

}