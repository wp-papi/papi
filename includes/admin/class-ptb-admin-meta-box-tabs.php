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
   * The tabs.
   *
   * @var array
   * @since 1.0.0
   */

  private $tabs = array();

  /**
   * Constructor.
   *
   * @param array $tabs
   * @since 1.0.0
   */

  public function __construct ($tabs = array()) {
    if (empty($tabs)) {
      return;
    }

    $this->setup_tabs($tabs);

    $this->html();
  }

  /**
   * Setup tabs array.
   *
   * @param array $tabs
   * @since 1.0.0
   * @access private
   */

  private function setup_tabs ($tabs) {
    $tabs = array_filter($tabs, function ($tab) {
      return _ptb_current_user_is_allowed($tab->options['capabilities'])
    });

    // Generate unique names for all tabs.
    for ($i = 0; $i < count($tabs); $i++) {
      $tabs[$i]->name = _ptb_name($tabs[$i]->title) . '_' . $i;
    }

    $this->tabs = $tabs;
  }

  /**
   * Generate html for tabs and properties.
   *
   * @since 1.0.0
   * @access private
   */

  private function html () {
    ?>
    <div class="ptb-tabs-wrapper">
      <div class="ptb-tabs-back"></div>
      <ul class="ptb-tabs">
      <?php
      foreach ($this->tabs as $tab):
        ?>
          <li class="<?php echo $this->tabs[0] == $tab ? 'active': ''; ?>">
            <a href="#" data-ptb-tab="<?php echo $tab->name; ?>">
              <?php if (isset($tab->options->icon) && !empty($tab->options->icon)): ?>
                <img src="<?php echo $tab->options->icon; ?>" alt="<?php echo $tab->title; ?>" />
              <?php endif;
              echo $tab->title; ?>
            </a>
          </li>
        <?php
      endforeach;
      ?>
    </ul>
    <div class="ptb-tabs-content">
      <?php
      foreach ($this->tabs as $tab):
        ?>
        <div class="<?php echo $this->tabs[0] == $tab ? 'active': ''; ?>" data-ptb-tab="<?php echo $tab->name; ?>">
          <?php _ptb_render_properties($tab->properties); ?>
        </div>
        <?php
      endforeach;
      ?>
    </div>
  </div>
  <div class="ptb-clear"></div>
  <?php
  }
}