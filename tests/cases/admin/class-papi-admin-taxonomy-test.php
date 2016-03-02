<?php

/**
 * @group admin
 */
class Papi_Admin_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );
	}

	public function test_setup_actions() {
		$admin = new Papi_Admin_Taxonomy;
		$this->assertSame( 10, has_action( 'admin_init', [$admin, 'setup_taxonomies_hooks'] ) );
	}
}
