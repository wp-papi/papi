<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Admin Meta Box Tabs.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PTB_Admin_Meta_Box_Tabs {

  /**
   * Tabs default options.
   *
   * @var array
   * @since 1.0.0
   */

  private $default_options = array(
    array(
      'name'       => '',
      'icon'       => '',
      'properties' => array()
    )
  );

  /**
   * Page Type Builder Admin Box Constructor.
   *
   * @param array $options
   * @param array $properties
   * @since 1.0.0
   */

  public function __construct ($options = array()) {
    if (!is_array($options)) {
      $options = array();
    }

    $this->options = array_merge($this->default_options, $options);
    $this->options = array_map(function ($tab) {
      return (object)$tab;
    }, $this->options);
  }

  /**
   * Generate html for tabs and properties.
   *
   * @since 1.0.0
   */

  private function html () {
    ?>
    <div class="ptb-tabs-back"></div>
    <ul class="ptb-tabs">
    <?php
    foreach ($this->options as $tab):
      ?>
        <li class="active">
          <a href="#" data-ptb-tab="<?php echo $tab->name; ?>">
            <?php if (isset($tab->icon) && !empty($tab->icon)): ?>
              <img src="<?php echo $tab->icon; ?>" alt="<?php echo $tab->name; ?>" />
            <?php endif; ?>
          </a>
        </li>
      <?php
    endforeach;
    ?>
  </ul>
  <div class="ptb-tabs-content">
    <?php
    foreach ($this->options as $tab):
      ?>
      <div class="active" data-ptb-tab="<?php echo $tab->name; ?>">
        <?php
        foreach ($tab->properties as $property):
          ?>
          
          <?php
        endforeach;
        ?>
      </div>
      <?php
    endforeach;
    ?>
  </div>
  }

}