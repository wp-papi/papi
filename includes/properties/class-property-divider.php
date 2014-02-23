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
    if (isset($this->get_options()->custom->css_class)) {
      $css_class = $this->get_options()->custom->css_class;
    } else {
      $css_class = '';
    }
    
    if (ptb_is_random_title($this->get_options()->title)) {
      return PTB_Html::tag('div', array(
        'class' => 'ptb-divider-no-text ' . $css_class
      ));
    } else {
      $span = PTB_Html::tag('span', $this->get_options()->title);
      return PTB_Html::tag('h3', $span, array(
        'class' => 'hndle ptb-divider-text ' . $css_class
      ));
    }
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0
   *
   * @return string
   */

  public function render () {
    $html = PTB_Html::td($this->html(), array('colspan' => 2));
    return PTB_Html::tr($html);
  }

}