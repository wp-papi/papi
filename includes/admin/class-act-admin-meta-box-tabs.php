<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Act Admin Meta Box Tabs.
 *
 * @package Act
 * @version 1.0.0
 */

class Act_Admin_Meta_Box_Tabs {

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
    // Check capabilities on tabs.
    $tabs = array_filter($tabs, function ($tab) {
      return _act_current_user_is_allowed($tab->options->capabilities);
    });

    // Sort tabs based on `sort_order` value.
    $tabs = _act_sort_order($tabs);

    // Generate unique names for all tabs.
    for ($i = 0; $i < count($tabs); $i++) {
      $tabs[$i]->name = _act_name($tabs[$i]->title) . '_' . $i;
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
    <div class="act-tabs-wrapper">
      <div class="act-tabs-back"></div>
      <ul class="act-tabs">
      <?php

      foreach ($this->tabs as $tab):
        ?>
          <li class="<?php echo $this->tabs[0] == $tab ? 'active': ''; ?>">
            <a href="#" data-act-tab="<?php echo $tab->name; ?>">
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
    <div class="act-tabs-content">
      <?php
      foreach ($this->tabs as $tab):
        ?>
        <div class="<?php echo $this->tabs[0] == $tab ? 'active': ''; ?>" data-act-tab="<?php echo $tab->name; ?>">
          <?php _act_render_properties($tab->properties); ?>
        </div>
        <?php
      endforeach;
      ?>
    </div>
  </div>
  <div class="act-clear"></div>
  <?php
  }
}
