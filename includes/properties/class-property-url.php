<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Act - Property Url
 *
 * @package Act
 * @version 1.0.0
 */

class PropertyUrl extends Act_Property {

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
      echo '&nbsp;' . Act_Html::input('submit', array(
        'name' => $options->slug . '_button',
        'data-act-action' => 'mediauploader',
        'value' => __('Select file', 'act'),
        'class' => 'button act-url-media-button'
      ));
    } else {
      echo Act_Html::input('url', array(
        'name'  => $options->slug,
        'value' => $value,
        'class' => $this->css_classes()
      ));
    }
  }

}
