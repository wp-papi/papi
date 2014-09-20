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
    $value = $this->get_value();

    ?>
    <input type="url" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="<?php echo $this->css_classes(); ?>" />

    <?php if ($settings->mediauploader): ?>
      &nbsp; <input type="submit" name="<?php echo $options->slug; ?>_button" value="<?php echo __('Select file', 'papi'); ?>" class="button papi-url-media-button" data-papi-action="mediauploader" />
    <?php endif;
  }

}