<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Admin Options Pages.
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PTB_Admin_Options_Pages {

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
   * Register option page.
   *
   * @since 1.0.0
   */

  public function admin_menu () {
    $ptb = page_type_builder();
    add_options_page($ptb->name, $ptb->name, 'manage_options', 'page-type-builder', array($this, 'render_view'));
  }

  /**
   * Render options page view.
   *
   * @since 1.0.0
   */

  public function render_view () {
    if (isset($_GET['view'])) {
      $page_view = $_GET['view'];
    } else {
      $page_view = 'options-start';
    }

    if (!is_null($page_view)) {
      $this->view->render($page_view);
    } else {
      echo '<h2>Page Type Builder - 404</h2>';
    }
  }

}

