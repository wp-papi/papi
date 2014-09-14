<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Admin Management Pages.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PTB_Admin_Management_Pages {

  /**
   * Thew view instance.
   *
   * @var PTB_Admin_View
   */

  private $view;

  /**
   * Constructor.
   */

  public function __construct () {
    // Setup globals.
    $this->setup_globals();

    // Setup actions.
    $this->setup_actions();
  }

  /**
   * Setup actions.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_actions () {
    add_action('admin_menu', array($this, 'admin_menu'));
  }

  /**
   * Setup globals.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_globals () {
    $this->view = new PTB_Admin_View;
  }

  /**
   * Register management page.
   *
   * @since 1.0.0
   */

  public function admin_menu () {
    $ptb = page_type_builder();
    add_management_page($ptb->name, $ptb->name, 'manage_options', 'page-type-builder', array($this, 'render_view'));
  }

  /**
   * Render tools page view.
   *
   * @since 1.0.0
   */

  public function render_view () {
    if (isset($_GET['view'])) {
      $page_view = $_GET['view'];
    } else {
      $page_view = 'management-start';
    }

    if (!is_null($page_view)) {
      $this->view->render($page_view);
    } else {
      echo '<h2>Page Type Builder - 404</h2>';
    }
  }

}

