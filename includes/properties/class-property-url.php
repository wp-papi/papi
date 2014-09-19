<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Papi - Property Url
 *
 * @package Papi
 * @version 1.0.0
 */

class PropertyUrl extends Papi_Property {

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
      echo '&nbsp;' . Papi_Html::input('submit', array(
        'name' => $options->slug . '_button',
        'data-papi-action' => 'mediauploader',
        'value' => __('Select file', 'papi'),
        'class' => 'button papi-url-media-button'
      ));
    } else {
      echo Papi_Html::input('url', array(
        'name'  => $options->slug,
        'value' => $value,
        'class' => $this->css_classes()
      ));
    }
  }

}
