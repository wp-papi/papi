<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Text
 */

class PropertyText extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0
   *
   * @return string
   */

  public function html () {
    return PTB_Html::textarea($this->get_options()->value, array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'class' => 'ptb-property-text'
    ));
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    $label = PTB_Html::td($this->label(), array('colspan' => 2));
    $label = PTB_Html::tr($label);
    $html = PTB_Html::td($this->html(), array('colspan' => 2));
    $html = PTB_Html::tr($html);
    return $label . $html;
  }

}