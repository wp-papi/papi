<?php

class Papi_Lib_Types_Data_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_data_type_exists() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->assertFalse( papi_data_type_exists( 'hello' ) );
		$this->assertTrue( papi_data_type_exists( 'empty-page-type' ) );
		$this->assertTrue( papi_data_type_exists( 'options/header-option-type' ) );
	}

	public function test_papi_get_all_data_types() {
		$this->assertEmpty( papi_get_all_data_types() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/data-types'];
		} );

		$this->assertNotEmpty( papi_get_all_data_types() );

		$output = papi_get_all_data_types( [
			'types' => 'data'
		] );
		$this->assertNotEmpty( $output );
		$this->assertSame( 'Info data type', $output[0]->name );
	}

	public function test_papi_get_all_data_types_option() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$output = papi_get_all_data_types( [
			'types' => 'option'
		] );

		$this->assertNotEmpty( $output );
		$this->assertSame( 'Header', $output[0]->name );
	}

	public function test_papi_get_data_type() {
		$this->assertNull( papi_get_data_type( 'hello.php' ) );
		$path = PAPI_FIXTURE_DIR . '/page-types/boxes/simple.php';
		$this->assertNull( papi_get_data_type( $path ) );
		$path = PAPI_FIXTURE_DIR . '/page-types/simple-page-type.php';
		$this->assertNotEmpty( papi_get_data_type( $path ) );
		$path = PAPI_FIXTURE_DIR . '/page-types2/look-page-type.php';
		$page_type = papi_get_data_type( $path );
		$this->assertTrue( $page_type instanceof Look_Module_Type );
	}

	public function test_papi_get_data_type_by_id() {
		$this->assertNull( papi_get_data_type_by_id( 0 ) );
		$this->assertNull( papi_get_data_type_by_id( [] ) );
		$this->assertNull( papi_get_data_type_by_id( (object) [] ) );
		$this->assertNull( papi_get_data_type_by_id( true ) );
		$this->assertNull( papi_get_data_type_by_id( false ) );
		$this->assertNull( papi_get_data_type_by_id( null ) );
		$this->assertNull( papi_get_data_type_by_id( 'page' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$simple_page_type = papi_get_data_type_by_id( 'simple-page-type' );
		$this->assertTrue( is_object( $simple_page_type ) );
	}

	public function test_papi_get_data_type_id() {
		$this->assertEmpty( papi_get_data_type_id() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertSame( 'simple-page-type', papi_get_data_type_id( $this->post_id ) );

		$_GET['page_type'] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_data_type_id() );
		unset( $_GET['page_type'] );

		$_POST[PAPI_PAGE_TYPE_KEY] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_data_type_id() );
		unset( $_POST[PAPI_PAGE_TYPE_KEY] );

		$post_parent = 'post_parent';
		$_GET[$post_parent] = $this->post_id;
		$this->assertSame( 'simple-page-type', papi_get_data_type_id() );
		unset( $_GET[$post_parent] );

		$this->assertEmpty( papi_get_data_type_id() );

		tests_add_filter( 'papi/core/load_one_type_on', function ( $post_types ) {
			$post_types[] = 'duck';
			return $post_types;
		} );

		$_GET['post_type'] = 'duck';
		$this->assertSame( 'duck-page-type', papi_get_data_type_id( 0 ) );

		tests_add_filter( 'papi/core/load_one_type_on', function ( $post_types ) {
			$post_types[] = 'attachment_test';
			return $post_types;
		} );

		$_GET['post_type'] = 'attachment';
		papi()->bind( 'core.page_type.attachment', 'others/attachment-type' );
		$this->assertSame( 'others/attachment-type', papi_get_data_type_id( 0 ) );
		papi()->remove( 'core.page_type.attachment' );

		$_GET['post_type'] = 'duck2';

		tests_add_filter( 'papi/core/load_one_type_on', function ( $post_types ) {
			$post_types[] = 'duck2';
			return $post_types;
		} );

		$this->assertEmpty( papi_get_page_type_id() );
		unset( $_GET['post_type'] );
	}
}
