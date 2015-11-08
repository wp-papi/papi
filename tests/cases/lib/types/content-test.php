<?php

class Papi_Lib_Types_Content_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_content_type_exists() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->assertFalse( papi_content_type_exists( 'hello' ) );
		$this->assertTrue( papi_content_type_exists( 'empty-page-type' ) );
		$this->assertTrue( papi_content_type_exists( 'options/header-option-type' ) );
	}

	public function test_papi_get_all_content_types() {
		$this->assertEmpty( papi_get_all_content_types() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/content-types'];
		} );

		$this->assertNotEmpty( papi_get_all_content_types() );

		$output = papi_get_all_content_types( [
			'types' => 'content'
		] );
		$this->assertNotEmpty( $output );
		$this->assertSame( 'Info content type', $output[0]->name );
	}

	public function test_papi_get_all_content_types_option() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$output = papi_get_all_content_types( [
			'types' => 'option'
		] );

		$this->assertNotEmpty( $output );
		$this->assertSame( 'Header', $output[0]->name );
	}

	public function test_papi_get_content_type() {
		$this->assertNull( papi_get_content_type( 'hello.php' ) );
		$path = PAPI_FIXTURE_DIR . '/page-types/boxes/simple.php';
		$this->assertNull( papi_get_content_type( $path ) );
		$path = PAPI_FIXTURE_DIR . '/page-types/simple-page-type.php';
		$this->assertNotEmpty( papi_get_content_type( $path ) );
		$path = PAPI_FIXTURE_DIR . '/page-types2/look-page-type.php';
		$page_type = papi_get_content_type( $path );
		$this->assertTrue( $page_type instanceof Look_Module_Type );
	}

	public function test_papi_get_content_type_by_id() {
		$this->assertNull( papi_get_content_type_by_id( 0 ) );
		$this->assertNull( papi_get_content_type_by_id( [] ) );
		$this->assertNull( papi_get_content_type_by_id( (object) [] ) );
		$this->assertNull( papi_get_content_type_by_id( true ) );
		$this->assertNull( papi_get_content_type_by_id( false ) );
		$this->assertNull( papi_get_content_type_by_id( null ) );
		$this->assertNull( papi_get_content_type_by_id( 'page' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$simple_page_type = papi_get_content_type_by_id( 'simple-page-type' );
		$this->assertTrue( is_object( $simple_page_type ) );
	}

	public function test_papi_get_content_type_id() {
		$this->assertEmpty( papi_get_content_type_id() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertSame( 'simple-page-type', papi_get_content_type_id( $this->post_id ) );

		$_GET['page_type'] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_content_type_id() );
		unset( $_GET['page_type'] );

		$_POST[PAPI_PAGE_TYPE_KEY] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_content_type_id() );
		unset( $_POST[PAPI_PAGE_TYPE_KEY] );

		$post_parent = 'post_parent';
		$_GET[$post_parent] = $this->post_id;
		$this->assertSame( 'simple-page-type', papi_get_content_type_id() );
		unset( $_GET[$post_parent] );

		$this->assertEmpty( papi_get_content_type_id() );
		$this->assertEmpty( papi_get_page_type_id() );
		unset( $_GET['post_type'] );

		$post_id = $this->factory->post->create();
		$this->assertEmpty( papi_get_content_type_id( $post_id ) );
	}
}
