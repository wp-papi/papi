<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Unit tests to check so Page Tyep Builder is loaded correctly.
 *
 * @package Act
 */

class WP_ACT_Plugin extends WP_UnitTestCase {

  /**
   * Test so Act plugin is loaded correct.
   */

  public function test_plugin_activated () {
    $this->assertTrue(class_exists('ACT_Loader') && class_exists('ACT_Admin'));
  }

  /**
   * The action `after_theme_setup` should have the `page_type_builder` hook
   * and should have a default priority of 10.
   */

  public function test_after_setup_theme_action () {
    $this->assertEquals(10, has_action('after_setup_theme', 'act'));
  }

}
