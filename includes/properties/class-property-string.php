<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property String
 *
 * @package PageTypeBuilder
 */

class PropertyString extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0.0
   *
   * @return string
   */

  public function html () {
    echo PTB_Html::input('text', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'value' => $this->get_options()->value,
      'class' => $this->css_classes()
    ));
  }

}