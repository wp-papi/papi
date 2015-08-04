<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering page functions.
 *
 * @package Papi
 */
class Papi_Lib_Page_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_display_page_type() {
		$this->assertFalse( papi_display_page_type( 'fake-page-type1' ) );

		$_GET['post_type'] = 'page';
		$this->assertFalse( papi_display_page_type( 'fake-page-type2' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type = papi_get_page_type_by_id( 'display-not-page-type' );
		$this->assertFalse( papi_display_page_type( $page_type ) );

		$page_type = papi_get_page_type_by_id( 'empty-page-type' );
		$this->assertTrue( papi_display_page_type( $page_type ) );

		tests_add_filter( 'papi/settings/show_page_type_page', function ( $page_type ) {
			if ( $page_type == 'simple-page-type' ) {
				return false;
			}

			return true;
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertFalse( papi_display_page_type( $page_type ) );

		$GET['post_type'] = 'module';

		$page_type = papi_get_page_type_by_id( 'faq-page-type' );
		$this->assertFalse( papi_display_page_type( $page_type ) );
	}

	public function test_papi_get_all_page_types() {
		$this->assertEmpty( papi_get_all_page_types() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$actual = papi_get_all_page_types();
		$this->assertTrue( empty( $actual ) );

		$actual = papi_get_all_page_types( true );
		$this->assertFalse( empty( $actual ) );
	}

	public function test_papi_get_page() {
		$page = papi_get_page( $this->post_id );
		$this->assertTrue( is_object( $page ) );
		$page = papi_get_page( $this->post_id, 'fake' );
		$this->assertNull( $page );
	}

	public function test_papi_get_page_type_by_post_id() {
		$this->assertNull( papi_get_page_type_by_post_id( 0 ) );
		$this->assertNull( papi_get_page_type_by_post_id( [] ) );
		$this->assertNull( papi_get_page_type_by_post_id( (object) [] ) );
		$this->assertNull( papi_get_page_type_by_post_id( true ) );
		$this->assertNull( papi_get_page_type_by_post_id( false ) );
		$this->assertNull( papi_get_page_type_by_post_id( null ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$this->assertTrue( is_object( papi_get_page_type_by_post_id( $this->post_id ) ) );

		$_GET['page_id'] = $this->post_id;
		$this->assertTrue( is_object( papi_get_page_type_by_post_id() ) );
		unset( $_GET['page_id'] );
	}

	public function test_papi_get_number_of_pages() {
		$this->assertEquals( 0, papi_get_number_of_pages( 'simple-page-type' ) );
		$this->assertEquals( 0, papi_get_number_of_pages( null ) );
		$this->assertEquals( 0, papi_get_number_of_pages( true ) );
		$this->assertEquals( 0, papi_get_number_of_pages( false ) );
		$this->assertEquals( 0, papi_get_number_of_pages( [] ) );
		$this->assertEquals( 0, papi_get_number_of_pages( new stdClass() ) );
		$this->assertEquals( 0, papi_get_number_of_pages( 1 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->flush_cache();
		$this->assertEquals( 1, papi_get_number_of_pages( 'simple-page-type' ) );

		$simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );

		$this->assertEquals( 1, papi_get_number_of_pages( $simple_page_type ) );
	}

	public function test_papi_get_page_type_template() {
		$this->assertNull( papi_get_page_type_template() );
		$this->assertNull( papi_get_page_type_template( 0 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$actual = papi_get_page_type_template( $this->post_id );
		$this->assertEquals( 'pages/simple-page.php', $actual );

		$_GET['page_id'] = $this->post_id;
		$this->assertEquals( 'pages/simple-page.php', papi_get_page_type_template() );
		unset( $_GET['page_id'] );
	}

	public function test_papi_get_page_type() {
		$this->assertNull( papi_get_page_type( 'hello.php' ) );
		$path = PAPI_FIXTURE_DIR . '/boxes/simple.php';
		$this->assertNull( papi_get_page_type( $path ) );
		$path = PAPI_FIXTURE_DIR . '/page-types/simple-page-type.php';
		$this->assertNotEmpty( papi_get_page_type( $path ) );
		$path = PAPI_FIXTURE_DIR . '/page-types2/look-page-type.php';
		$page_type = papi_get_page_type( $path );
		$this->assertTrue( $page_type instanceof Look_Module_Type );
	}

	public function test_papi_get_page_type_by_id() {
		$this->assertNull( papi_get_page_type_by_id( 0 ) );
		$this->assertNull( papi_get_page_type_by_id( [] ) );
		$this->assertNull( papi_get_page_type_by_id( (object) [] ) );
		$this->assertNull( papi_get_page_type_by_id( true ) );
		$this->assertNull( papi_get_page_type_by_id( false ) );
		$this->assertNull( papi_get_page_type_by_id( null ) );
		$this->assertNull( papi_get_page_type_by_id( 'page' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertTrue( is_object( $simple_page_type ) );
	}

	public function test_papi_get_page_type_id() {
		$this->assertEmpty( papi_get_page_type_id() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertEquals( 'simple-page-type', papi_get_page_type_id( $this->post_id ) );

		$_GET['page_type'] = 'simple-page-type';
		$this->assertEquals( 'simple-page-type', papi_get_page_type_id() );
		unset( $_GET['page_type'] );

		$_POST[PAPI_PAGE_TYPE_KEY] = 'simple-page-type';
		$this->assertEquals( 'simple-page-type', papi_get_page_type_id() );
		unset( $_POST[PAPI_PAGE_TYPE_KEY] );

		$from_post = papi_filter_settings_page_type_from_post_qs();
		$_GET[$from_post] = $this->post_id;
		$this->assertEquals( 'simple-page-type', papi_get_page_type_id() );
		unset( $_GET[$from_post] );

		tests_add_filter( 'papi/settings/page_type_from_post_qs', function () {
			return '';
		} );

		$this->assertEmpty( papi_get_page_type_id() );

	}

	public function test_papi_get_post_types() {
		$actual = papi_get_post_types();

		foreach ( $actual as $key => $value ) {
			if ( $value !== 'page' ) {
				unset( $actual[$key] );
			}
		}

		$this->assertEquals( ['page'], array_values( $actual ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$post_types = papi_get_post_types();

		$this->assertTrue( in_array( 'page', $post_types ) );
	}

	public function test_papi_get_page_type_key() {
		$this->assertEquals( '_papi_page_type', papi_get_page_type_key() );
	}

	public function test_papi_get_page_type_name() {
		$this->assertEmpty( papi_get_page_type_name() );
		$this->assertEmpty( papi_get_page_type_name( null ) );
		$this->assertEmpty( papi_get_page_type_name( 0 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		global $post;
		$post = get_post( $this->post_id );

		$this->assertEquals( 'Simple page', papi_get_page_type_name() );
		$this->assertEquals( 'Simple page', papi_get_page_type_name( $this->post_id ) );
	}

	public function test_papi_get_slugs() {
		$this->assertEmpty( papi_get_slugs() );

		global $post;

		$post = get_post( $this->post_id );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$actual = papi_get_slugs( $this->post_id );

		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '' );
		$this->flush_cache();
		$this->assertEmpty( papi_get_slugs() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->flush_cache();
		$this->assertEmpty( papi_get_slugs() );
	}

	public function test_the_papi_page_type_name() {
		the_papi_page_type_name();
		$this->expectOutputRegex( '//' );

		the_papi_page_type_name( null );
		$this->expectOutputRegex( '//' );

		the_papi_page_type_name( 0 );
		$this->expectOutputRegex( '//' );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		global $post;
		$post = get_post( $this->post_id );

		the_papi_page_type_name();
		$this->expectOutputRegex( '/Simple\spage/' );

		the_papi_page_type_name( $this->post_id );
		$this->expectOutputRegex( '/Simple\spage/' );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '' );
		the_papi_page_type_name();
		$this->expectOutputRegex( '//' );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'random322-page-type' );
		the_papi_page_type_name();
		$this->expectOutputRegex( '//' );
	}
}
