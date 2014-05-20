<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Text
 *
 * @package PageTypeBuilder
 */

class PropertyText extends PTB_Property {

  /**
   * Get the html for output.
   *
   * @since 1.0.0
   *
   * @return string
   */

  public function html () {
    $options = $this->get_options();
    $custom = $this->get_custom_options(array(
      'wp_editor' => false
    ));

    if ($custom->wp_editor) {
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
   * Render the final html that is displayed in the table.
   *
   * @since 1.0.0
   *
   * @return string
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