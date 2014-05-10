<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Core.
 */

final class PTB_Core {

  /**
   * The instance of Page Type Builder Core.
   *
   * @var object
   * @since 1.0
   */

  private static $instance;

  /**
   * Page Type Bulider Core instance.
   *
   * @since 1.0
   *
   * @return object
   */

  public static function instance () {
    if (!isset(self::$instance)) {
      self::$instance = new PTB_Core;
      self::$instance->setup_globals();
      self::$instance->setup_actions();
      self::$instance->setup_filters();
    }
    return self::$instance;
  }

  /**
   * The settings for Page Type Builder
   * Can be overriden by the filter `ptb_settings`.
   *
   * @var array
   * @since 1.0
   */

  private $settings = array();

  /**
   * Page Type Builder Core Constructor.
   *
   * @since 1.0.0
   */

  public function __construct () {
    if (function_exists('__autoload')) {
      spl_autoload_register('__autoload');
    }

    spl_autoload_register(array($this, 'autoload'));
  }

  /**
   * Setup globals.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_globals () {
    $this->admin = PTB_Admin::instance();
    $this->settings = apply_filters('ptb_settings', $this->settings);
  }

  /**
   * Setup actions.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_actions () {}

  /**
   * Setup filters.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_filters () {}

  /**
   * Get settings array.
   *
   * @since 1.0.0
   *
   * @return array
   */

  public function get_settings () {
    return $this->settings;
  }

  /**
   * Auto load Page Type Builder classes on demand.
   *
   * @param mixed $class
   */

  public function autoload ($class) {
    $path = null;
    $class = strtolower($class);
    $file = 'class-' . str_replace( '_', '-', $class ) . '.php';

    if (strpos($class, 'ptb_admin') === 0) {
      $path = PTB_PLUGIN_DIR . 'includes/admin/';
    } else if (strpos($class, 'ptb') === 0) {
      $path = PTB_PLUGIN_DIR . 'includes/';
    }

    if (!is_null($path) && is_readable($path . $file)) {
      require_once($path . $file);
      return;
    }
  }
}