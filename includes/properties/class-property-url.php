<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Url
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyUrl extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Property settings.
    $settings = $this->get_settings(array(
      'mediauploader' => false
    ));

    // Database value.
    $value = $this->get_value('');

    if ($settings->mediauploader) {
      echo $html .= '&nbsp;' . PTB_Html::input('submit', array(
        'name' => $options->name . '_button',
        'data-ptb-action' => 'mediauploader',
        'value' => __('Select file', 'ptb'),
        'class' => 'button ptb-url-media-button'
      ));
    } else {
      echo PTB_Html::input('url', array(
        'name'  => $options->name,
        'id'    => $options->name,
        'value' => $value,
        'class' => $this->css_classes()
      ));
    }
  }

}