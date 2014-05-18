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
    if (isset($this->get_options()->custom->mediauploader) && $this->get_options()->custom->mediauploader) {
      echo $html .= '&nbsp;' . PTB_Html::input('submit', array(
        'name' => $this->get_options()->name . '_button',
        'data-ptb-action' => 'mediauploader',
        'value' => __('Select file', 'ptb'),
        'class' => 'button ptb-url-media-button'
      ));
    } else {
      echo PTB_Html::input('url', array(
        'name' => $this->get_options()->name,
        'id' => $this->get_options()->name,
        'value' => $this->get_options()->value,
        'class' => $this->css_classes()
      ));
    }
  }

}