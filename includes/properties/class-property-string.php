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
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Database value.
    $value = $this->get_value('');

    echo PTB_Html::input('text', array(
      'name'  => $options->name,
      'id'    => $options->name,
      'value' => $value,
      'class' => $this->css_classes()
    ));
  }

}