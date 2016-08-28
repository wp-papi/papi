<?php

/**
 * @group admin
 */
class Papi_Admin_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		papi()->reset();
	}

	public function test_add_form_fields_empty() {
		$admin = new Papi_Admin_Taxonomy;
		$admin->add_form_fields();
		$this->expectOutputRegex( '//' );
	}

	public function test_add_form_fields_single() {
		papi()->reset();

		add_filter( 'papi/settings/directories', function () {
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
		papi()->reset();

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		add_filter( 'papi/settings/show_standard_taxonomy_type_category', '__return_true' );

		$_GET['taxonomy'] = 'category';

		$admin = new Papi_Admin_Taxonomy;
		$admin->setup_taxonomies_hooks();
		$admin->add_form_fields();
		$this->expectOutputRegex( '/select.*name\=\"\_papi\_page\_type\"/' );

		unset( $_GET['taxonomy'] );
	}

	public function test_add_form_fields_several() {
		papi()->reset();

		add_filter( 'papi/settings/directories', function () {
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
		papi()->reset();

		$admin = new Papi_Admin_Taxonomy;
		$this->assertSame( 10, has_action( 'admin_init', [$admin, 'setup_taxonomies_hooks'] ) );
		$this->assertFalse( has_action( 'category_add_form_fields', [$admin, 'add_form_fields'] ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$admin->setup_taxonomies_hooks();
		$this->assertSame( 10, has_action( 'category_add_form_fields', [$admin, 'add_form_fields'] ) );
	}
}
