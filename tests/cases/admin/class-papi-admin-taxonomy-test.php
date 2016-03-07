<?php

/**
 * @group admin
 */
class Papi_Admin_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		papi()->remove( 'papi_get_all_core_type_files' );
	}

	public function test_add_form_fields_empty() {
		$admin = new Papi_Admin_Taxonomy;
		$admin->add_form_fields();
		$this->expectOutputRegex( '//' );
	}

	public function test_add_form_fields_single() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$_GET['taxonomy'] = 'category';

		$admin = new Papi_Admin_Taxonomy;
		$admin->setup_taxonomies_hooks();
		$admin->add_form_fields();
		$this->expectOutputRegex( '/input.*name\=\"\_papi\_page\_type\"/' );

		unset( $_GET['taxonomy'] );
	}

	public function test_add_form_fields_single_plus_standard() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		tests_add_filter( 'papi/settings/show_standard_taxonomy_type_category', '__return_true' );

		$_GET['taxonomy'] = 'category';

		$admin = new Papi_Admin_Taxonomy;
		$admin->setup_taxonomies_hooks();
		$admin->add_form_fields();
		$this->expectOutputRegex( '/select.*name\=\"\_papi\_page\_type\"/' );

		unset( $_GET['taxonomy'] );
	}

	public function test_add_form_fields_several() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$_GET['taxonomy'] = 'post_tag';

		$admin = new Papi_Admin_Taxonomy;
		$admin->setup_taxonomies_hooks();
		$admin->add_form_fields();
		$this->expectOutputRegex( '/select.*name\=\"\_papi\_page\_type\"/' );

		unset( $_GET['taxonomy'] );
	}

	public function test_setup_actions() {
		$admin = new Papi_Admin_Taxonomy;
		$this->assertSame( 10, has_action( 'admin_init', [$admin, 'setup_taxonomies_hooks'] ) );
		$this->assertFalse( has_action( 'category_add_form_fields', [$admin, 'add_form_fields'] ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$admin->setup_taxonomies_hooks();
		$this->assertSame( 10, has_action( 'category_add_form_fields', [$admin, 'add_form_fields'] ) );
	}
}
