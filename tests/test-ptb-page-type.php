<?php

/**
 * Unit tests covering page type functionality.
 *
 * @package PageTyepBuilder
 */

class WP_PTB_Page_Type extends WP_UnitTestCase {

  /**
   * Setup the test and register the page types directory.
   */

  public function setUp () {
    parent::setUp();

    register_ptb_directory(getcwd() . '/tests/data/page-types/');
  }

  /**
   * Test so we acctually has any page type files.
   */

  public function test_ptb_get_all_page_types () {
    $page_types = _ptb_get_all_page_types(true);
    $this->assertTrue(!empty($page_types));
  }

  /**
   * Test slug generation.
   */

  public function test_slug () {
    $slug = _test_ptb_generate_slug('heading');
    $this->assertEquals($slug, '_ptb_en_heading');

    $slug = _ptb_property_type_key($slug);
    $this->assertEquals($slug, '_ptb_en_heading_property');
  }

  /**
   * Test creating a fake property data via `add_post_meta`.
   */

  public function test_ptb_field () {
    $post_id = $this->factory->post->create();

    $slug = _test_ptb_generate_slug('heading');
    add_post_meta($post_id, $slug, 'page type builder');

    $slug = _ptb_property_type_key($slug);
    add_post_meta($post_id, $slug, 'PropertyString');

    $heading = ptb_field($post_id, 'heading');
    $this->assertEquals($heading, 'page type builder');

    $heading_property = ptb_field($post_id, 'heading_property');
    $this->assertEquals($heading_property, 'PropertyString');
  }

}