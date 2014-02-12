<?php

class PTB_Core {

  /**
   * Constructor. Add actions.
   */

  public function __construct () {
    add_action('admin_menu', array($this, 'ptb_menu'));
  }

  /**
   * Build up the sub menu for "Page".
   */

  public function ptb_menu () {
    // Remove "Add new" menu item.
    remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
    // Add our custom menu item.
    add_submenu_page('edit.php?post_type=page', 'Add new', 'Add new', 'manage_options', 'ptb-add-new-page', array($this, 'ptb_add_new_page'));
  }

  /**
   * Add new page output.
   *
   * @todo Make this dynamic.
   */

  public function ptb_add_new_page () {
    echo 'hej';
  }

}

new PTB_Core;