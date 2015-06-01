<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering template functions.
 *
 * @package Papi
 */

class Papi_Lib_Template_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 */

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create( [
			'post_type' => 'page'
		] );
	}

	/**
	 * Tear down test.
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	/**
	 * Test `papi_body_class` function.
	 */

	public function test_papi_body_class() {
		global $post;

		$this->assertEmpty( papi_body_class( [] ) );

		$post = get_post( $this->post_id );
		$this->go_to( get_permalink( $this->post_id ) );
		$this->assertEmpty(  papi_body_class( [] ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '/' );
		$this->assertEmpty( papi_body_class( [] ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertEquals( [ 'simple-page-type' ], papi_body_class( [] )  );
	}

	/**
	 * Test `papi_include_template` function.
	 */

	public function test_papi_include_template() {
		$this->assertEmpty( papi_include_template( null ) );
		$this->assertEmpty( papi_include_template( [] ) );
		$this->assertEmpty( papi_include_template( new stdClass ) );
		$this->assertEmpty( papi_include_template( true ) );
		$this->assertEmpty( papi_include_template( false ) );

		papi_include_template( 'includes/admin/views/add-new-page.php' );
		$this->expectOutputRegex( '/Add\snew\spage/' );
	}

	/**
	 * Test `papi_template` function.
	 */

	public function test_papi_template() {
		$template = papi_template( PAPI_FIXTURE_DIR . '/properties/simple.php' );

		$this->assertEquals( 'Name', $template->title );
		$this->assertEquals( 'string', $template->type );

		$this->assertEmpty( papi_template( null ) );
		$this->assertEmpty( papi_template( true ) );
		$this->assertEmpty( papi_template( false ) );
		$this->assertEmpty( papi_template( 1 ) );
		$this->assertEmpty( papi_template( [] ) );
		$this->assertEmpty( papi_template( new stdClass() ) );
		$this->assertEmpty( papi_template( PAPI_FIXTURE_DIR ) );

		$template = papi_template( PAPI_FIXTURE_DIR . '/properties/array.php', [], true );

		$this->assertEquals( 'Name', $template->title );
		$this->assertEquals( 'string', $template->type );

		$this->assertEmpty( papi_template( 'hello' )  );
	}

	/**
	 * Test `template_include` filter.
	 */

	public function test_papi_template_include() {
		global $post;

		$this->assertEmpty( apply_filters( 'template_include', '' ) );

		$post = get_post( $this->post_id );
		$this->go_to( get_permalink( $this->post_id ) );
		$this->assertEmpty( apply_filters( 'template_include', '' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertEmpty( apply_filters( 'template_include', '' ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'twenty-page-type' );

		$path = get_template_directory();
		$path = trailingslashit( $path );
		$file = $path . 'functions.php';
		$path = apply_filters( 'template_include', '' );
		$this->assertNotFalse( strpos( $path, 'functions.php' ) );

	}

}
