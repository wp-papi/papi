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

  private $tabs = array(
    /*
    array(
      'name'       => '',
      'icon'       => '',
      'properties' => array()
    )*/
  );

  /**
   * Page Type Builder Admin Meta Box Tabs Constructor.
   *
   * @param array $tabs
   * @since 1.0.0
   */

  public function __construct ($tabs = array()) {
    $this->tabs = $tabs;
    echo'<pre>';
    var_dump($this->tabs);
    die();
    $this->html();
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
    foreach ($this->tabs as $tab):
      ?>
        <li class="active">
          <a href="#" data-ptb-tab="<?php echo $tab->title; ?>">
            <?php if (isset($tab->icon) && !empty($tab->icon)): ?>
              <img src="<?php echo $tab->icon; ?>" alt="<?php echo $tab->title; ?>" />
            <?php endif; ?>
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
      <div class="active" data-ptb-tab="<?php echo $tab->title; ?>">
        <?php

        ?>
      </div>
      <?php
    endforeach;
    ?>
  </div>
  <?php
  }
}