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
   * Constructor.
   *
   * @since 1.0.0
   */

  public function __construct () {}

  /**
   * Setup globals.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_globals () {
    $this->admin = PTB_Admin::instance();
  }

  /**
   * Get settings array.
   *
   * @since 1.0.0
   *
   * @return array
   */

  public function get_settings () {
    return apply_filters('ptb_settings', $this->settings);
  }
}