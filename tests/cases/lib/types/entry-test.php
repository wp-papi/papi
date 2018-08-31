<?php

class Papi_Lib_Types_Entry_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );

		remove_all_filters( 'papi/settings/directories' );
		papi()->reset();
	}

	public function test_papi_get_entry_type_body_classes() {
		global $post;

		$post = get_post( $this->post_id );
		$this->assertEmpty( papi_get_entry_type_body_classes() );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'dot-page-type' );
		$this->assertSame( ['custom-css-class'], papi_get_entry_type_body_classes() );
	}

	public function test_papi_get_entry_type_css_class_page() {
		global $post;

		$this->assertEmpty( papi_get_entry_type_css_class() );

		$post = get_post( $this->post_id );
		$this->go_to( get_permalink( $this->post_id ) );
		$this->assertEmpty( papi_get_entry_type_css_class() );

		update_post_meta( $this->post_id, papi_get_page_type_key(), '/' );
		$this->assertEmpty( papi_get_entry_type_css_class() );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertSame( 'simple-page-type', papi_get_entry_type_css_class() );
	}

	public function test_test_papi_get_entry_type_css_class_taxonomy() {
		$cat_id = $this->factory->category->create();
		$this->go_to( get_term_link( $cat_id, 'category' ) );
		$this->assertEmpty( papi_get_entry_type_css_class() );

		update_term_meta( $cat_id, papi_get_page_type_key(), '/' );
		$this->assertEmpty( papi_get_entry_type_css_class() );

		update_term_meta( $cat_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertSame( 'simple-page-type', papi_get_entry_type_css_class() );
	}

	public function test_papi_get_entry_type_count() {
		$this->assertSame( 0, papi_get_entry_type_count( 'simple-page-type' ) );
		$this->assertSame( 0, papi_get_entry_type_count( null ) );
		$this->assertSame( 0, papi_get_entry_type_count( true ) );
		$this->assertSame( 0, papi_get_entry_type_count( false ) );
		$this->assertSame( 0, papi_get_entry_type_count( [] ) );
		$this->assertSame( 0, papi_get_entry_type_count( new stdClass() ) );
		$this->assertSame( 0, papi_get_entry_type_count( 1 ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertSame( 1, papi_get_entry_type_count( 'simple-page-type' ) );

		$simple_page_type = papi_get_entry_type_by_id( 'simple-page-type' );

		$this->assertSame( 1, papi_get_entry_type_count( $simple_page_type ) );
	}

	public function test_papi_entry_type_exists() {
		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->assertFalse( papi_entry_type_exists( 'hello' ) );
		$this->assertTrue( papi_entry_type_exists( 'empty-page-type' ) );
		$this->assertTrue( papi_entry_type_exists( 'options/header-option-type' ) );
	}

	public function test_papi_get_all_entry_types() {
		$this->assertEmpty( papi_get_all_entry_types( ['cache' => false] ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/entry-types'];
		} );

		papi()->reset();

		$this->assertNotEmpty( papi_get_all_entry_types() );

		$output = papi_get_all_entry_types( [
			'types' => 'entry'
		] );
		$this->assertNotEmpty( $output );
		$this->assertSame( 'Info entry type', $output[0]->name );
	}

	public function test_papi_get_all_entry_types_with_same_id() {
		add_filter( 'papi/settings/directories', function () {
			return [PAPI_FIXTURE_DIR . '/entry-types', PAPI_FIXTURE_DIR . '/entry-types2'];
		} );

		papi()->reset();

		$output = papi_get_all_entry_types( [
			'types' => 'entry'
		] );

		$classes = array_map( 'get_class', array_values( $output ) );
		$this->assertTrue( in_array( 'Term_Entry_Type', $classes ) );
		$this->assertTrue( strpos( 'entry-types2/term-entry-type.php', $output[0]->get_file_path() ) !== -1 );
	}

	public function test_papi_get_all_entry_types_option() {
		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		papi()->reset();

		$output = papi_get_all_entry_types( [
			'types' => 'option'
		] );

		$this->assertNotEmpty( $output );
		$this->assertSame( 'Header', $output[0]->name );
	}

	public function test_papi_get_entry_type() {
		$this->assertNull( papi_get_entry_type( 'hello.php' ) );

		$path = PAPI_FIXTURE_DIR . '/page-types/boxes/simple.php';
		$this->assertNull( papi_get_entry_type( $path ) );

		$path = PAPI_FIXTURE_DIR . '/entry-types/base-entry-type.php';
		$this->assertNull( papi_get_entry_type( $path ) );

		$path = PAPI_FIXTURE_DIR . '/page-types/simple-page-type.php';
		$this->assertNotEmpty( papi_get_entry_type( $path ) );

		$path = PAPI_FIXTURE_DIR . '/page-types2/look-page-type.php';
		$page_type = papi_get_entry_type( $path );
		$this->assertTrue( $page_type instanceof Look_Module_Type );
	}

	public function test_papi_get_entry_type_by_id() {
		$this->assertNull( papi_get_entry_type_by_id( 0 ) );
		$this->assertNull( papi_get_entry_type_by_id( [] ) );
		$this->assertNull( papi_get_entry_type_by_id( (object) [] ) );
		$this->assertNull( papi_get_entry_type_by_id( true ) );
		$this->assertNull( papi_get_entry_type_by_id( false ) );
		$this->assertNull( papi_get_entry_type_by_id( null ) );
		$this->assertNull( papi_get_entry_type_by_id( 'page' ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$simple_page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$this->assertTrue( is_object( $simple_page_type ) );

		$settings_option_type = papi_get_entry_type_by_id( 'options/settings-option-type' );
		$this->assertTrue( is_object( $settings_option_type ) );
	}

	public function test_papi_get_entry_type_by_meta_id() {
		$this->assertNull( papi_get_entry_type_by_meta_id( 0 ) );
		$this->assertNull( papi_get_entry_type_by_meta_id( [] ) );
		$this->assertNull( papi_get_entry_type_by_meta_id( (object) [] ) );
		$this->assertNull( papi_get_entry_type_by_meta_id( true ) );
		$this->assertNull( papi_get_entry_type_by_meta_id( false ) );
		$this->assertNull( papi_get_entry_type_by_meta_id( null ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		$this->assertTrue( is_object( papi_get_entry_type_by_meta_id( $this->post_id ) ) );

		$_GET['page_id'] = $this->post_id;
		$this->assertTrue( is_object( papi_get_entry_type_by_meta_id() ) );
		unset( $_GET['page_id'] );
	}

	public function test_papi_get_entry_type_template() {
		$this->assertNull( papi_get_entry_type_template( null ) );
		$this->assertNull( papi_get_entry_type_template( 0 ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types',  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertSame( 'pages/simple-page.php', papi_get_entry_type_template( $this->post_id ) );

		$term_id = $this->factory->term->create();
		update_term_meta( $term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );
		$this->assertSame( 'pages/simple-taxonomy.php', papi_get_entry_type_template( $term_id, 'term' ) );
	}

	public function test_papi_get_entry_type_template_array() {
		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types',  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'modules/video-module-type' );
		$this->assertSame( 'video-a.php', papi_get_entry_type_template( $this->post_id ) );
	}

	public function test_papi_get_entry_type_template_array2() {
		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types',  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'modules/string-module-type' );
		$this->assertSame( 'string-a.php', papi_get_entry_type_template( $this->post_id ) );
	}

	public function test_papi_get_entry_type_id() {
		$_GET['entry_type'] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_entry_type_id() );
		unset( $_GET['entry_type'] );

		$post_id = $this->factory->post->create();
		$this->assertEmpty( papi_get_entry_type_id( $post_id ) );
	}

	public function test_papi_is_entry_type() {
		$this->assertFalse( papi_is_entry_type( 'fake-entry-type' ) );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->assertFalse( papi_is_entry_type( 'info-entry-type' ) );

		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, papi_get_page_type_key(), 'info-entry-type' );

		global $post;
		$post = get_post( $post_id );

		// Returns true if id equals entry type.
		$this->assertTrue( papi_is_entry_type( 'info-entry-type' ) );

		// Returns true if not empty.
		$this->assertTrue( papi_is_entry_type() );
	}
}
