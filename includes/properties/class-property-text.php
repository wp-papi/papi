<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Text
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyText extends PTB_Property {

 /**
  * Generate the HTML for the property.
  *
  * @since 1.0.0
  */

  public function html () {
    // Property options.
    $options = $this->get_options();

    // Database value. Can be null.
    $value = $this->get_value();

    // Property settings from the page type.
    $settings = $this->get_settings(array(
      'editor' => true,
    ));

    if ($settings->editor) {
      wp_editor($options->value, $options->name, array(
        'textarea_name' => $options->name
      ));
    } else {
      echo PTB_Html::textarea($options->value, array(
        'name' => $options->name,
        'id' => $options->name,
        'class' => $this->css_classes('ptb-property-text')
      ));
    }
  }

  /**
   * Render the final html that is displayed in the tabl
   * or without a table.
   *
   * @since 1.0.0
   */

  public function render () {
    $options = $this->get_options();
    if ($options->table): ?>
    <tr>
      <td <?php echo $options->colspan; ?>>
        <?php $this->label(); ?>
      </td>
    </tr>
    <tr>
      <td <?php echo $options->colspan; ?>>
        <?php $this->html(); ?>
      </td>
    </tr>
    <?php
      $this->helptext(false);
    else:
      $this->label();
      $this->html();
      $this->helptext(false);
    endif;
  }

}