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

}