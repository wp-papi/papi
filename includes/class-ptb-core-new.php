<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder Core.
 */

final class PTB_Core {

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
    // Setup globals.
    $this->setup_globals();

    // Setup actions.
    $this->setup_actions();

    // Setup filters.
    $this->setup_filters();
  }

  /**
   * Setup globals.
   *
   * @since 1.0.0
   * @access private
   */

  private function setup_globals () {
    $this->view = new PTB_View;
    $this->meta_box = new PTB_Meta_Box;
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

