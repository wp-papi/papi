<?php

/**
 * Page Type Builder Core class.
 */

class PTB_Core {

  /**
   * Constructor. Add actions.
   *
   * @since 1.0
   */

  public function __construct () {
    $this->view = new PTB_View;
    add_action('admin_menu', array($this, 'ptb_menu'));
    add_action('plugins_loaded', array($this, 'ptb_load'));
  }

  /**
   * Build up the sub menu for "Page".
   *
   * @since 1.0
   */

  public function ptb_menu () {
    // Remove "Add new" menu item.
    remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
    // Add our custom menu item.
    add_submenu_page('edit.php?post_type=page', __('Add new', 'ptb'), __('Add new', 'ptb'), 'manage_options', 'ptb-add-new-page', array($this, 'ptb_view'));
  }

  /**
   * Menu callback that loads right view depending on what the "page" query string says.
   *
   * @since 1.0
   */

  public function ptb_view () {
    $page_view = get_ptb_page_view();

    if (!is_null($page_view)) {
      $this->view->display($page_view);
    } else {
      echo '<h2>Page Type Builder - 404</h2>';
    }
  }

  /**
   * Load right Page Type Builder file if it exists.
   */

  public function ptb_load () {
    $uri = $_SERVER['REQUEST_URI'];

    // Only load Page Types on a "page" post type page in admin.
    if (strpos($uri, 'post-new.php?post_type=page') === false ||
      isset($_GET['post']) && get_post_type($_GET['post']) == 'page') {
      return;
    }

    $page_type = get_ptb_page_type();
    $path = PTB_DIR . 'ptb-' . $page_type . '.php';

    // Can't proceed without a page type or if the file exists.
    if (is_null($page_type) || !file_exists($path)) {
      return;
    }

    $class_name = get_class_name($path);

    // No class found.
    if (is_null($class_name)) {
      return;
    }

    require_once($path);
    new $class_name;
  }
}