<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Unit tests covering page type functionality.
 *
 * @package Act
 */

class WP_ACT_Page_Type extends WP_UnitTestCase {

  /**
   * Setup the test and register the page types directory.
   */

  public function setUp () {
    parent::setUp();

    register_act_directory(getcwd() . '/tests/data/page-types');
  }

  /**
   * Test so we acctually has any page type files.
   */

  public function test_act_get_all_page_types () {
    $page_types = _act_get_all_page_types(true);
    $this->assertTrue(!empty($page_types));
  }

  /**
   * Test slug generation.
   */

  public function test_slug () {
    $slug = _test_act_generate_slug('heading');
    $this->assertEquals($slug, '_act_heading');

    $slug = _act_property_type_key($slug);
    $this->assertEquals($slug, '_act_heading_property');
  }

  /**
   * Test creating a fake property data via `add_post_meta`.
   */

  public function test_act_field () {
    $post_id = $this->factory->post->create();

    $slug = _test_act_generate_slug('heading');
    add_post_meta($post_id, $slug, 'act');

    $slug = _act_property_type_key($slug);
    add_post_meta($post_id, $slug, 'PropertyString');

    $heading = act_field($post_id, 'heading');
    $this->assertEquals($heading, 'act');

    $heading_property = act_field($post_id, 'heading_property');
    $this->assertEquals($heading_property, 'PropertyString');
  }

}
