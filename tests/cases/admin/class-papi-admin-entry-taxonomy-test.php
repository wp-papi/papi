<?php

/**
 * @group admin
 */
class Papi_Admin_Entry_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		papi()->reset();
	}

	public function tearDown() {
		parent::tearDown();

		unset( $_GET );
	}

	public function test_add_form_fields_empty() {
		$admin = new Papi_Admin_Entry_Taxonomy;
		$admin->add_form_fields();
		$this->expectOutputRegex( '//' );
	}

	public function test_add_form_fields_single() {
		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$_GET['taxonomy'] = 'category';

		$admin = new Papi_Admin_Entry_Taxonomy;
		$admin->setup_taxonomies_hooks();
		$admin->add_form_fields();
		$this->expectOutputRegex( '/input.*name\=\"\_papi\_page\_type\"/' );
	}

	public function test_add_form_fields_single_plus_standard() {
		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		add_filter( 'papi/settings/show_standard_taxonomy_type_category', '__return_true' );

		$_GET['taxonomy'] = 'category';

		$admin = new Papi_Admin_Entry_Taxonomy;
		$admin->setup_taxonomies_hooks();
		$admin->add_form_fields();
		$this->expectOutputRegex( '/select.*name\=\"\_papi\_page\_type\"/' );
	}

	public function test_add_form_fields_several() {
		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$_GET['taxonomy'] = 'post_tag';

		$admin = new Papi_Admin_Entry_Taxonomy;
		$admin->setup_taxonomies_hooks();
		$admin->add_form_fields();
		$this->expectOutputRegex( '/select.*name\=\"\_papi\_page\_type\"/' );
	}

	public function test_setup_actions() {
		$admin = new Papi_Admin_Entry_Taxonomy;
		$this->assertSame( 10, has_action( 'admin_init', [$admin, 'setup_taxonomies_hooks'] ) );
		$this->assertFalse( has_action( 'category_add_form_fields', [$admin, 'add_form_fields'] ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$admin->setup_taxonomies_hooks();
		$this->assertSame( 10, has_action( 'category_add_form_fields', [$admin, 'add_form_fields'] ) );
	}
}
