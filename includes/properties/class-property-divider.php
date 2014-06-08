<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Divider
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyDivider extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    // Property options
    $options = $this->get_options();

    if (_ptb_is_random_title($options->title)) {
      echo PTB_Html::tag('div', array(
        'class' => $this->css_classes('ptb-divider-no-text')
      ));
    } else {
      $span = PTB_Html::tag('span', $options->title);
      echo PTB_Html::tag('h3', $span, array(
        'class' => $this->css_classes('hndle ptb-divider-text')
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
      <tr class="ptb-fullwidth">
        <td colspan="2">
          <?php $this->html(); ?>
        </td>
      </tr>
    <?php
    else:
      $this->html();
    endif;
  }

}