<?php

class Papi_Lib_Core_Deprecated_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET = [];

		add_action( 'deprecated_function_run', [$this, 'deprecated_function_run'] );
		add_filter( 'papi/settings/directories', function () {
			return [1, PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
	}

	public function deprecated_function_run( $function ) {
		add_filter( 'deprecated_function_trigger_error', '__return_false' );
	}

	public function tearDown() {
		parent::tearDown();
		remove_filter( 'deprecated_function_trigger_error', '__return_false' );
		unset( $_GET, $this->post_id );
	}

	/**
	 * `papi_get_page` is deprecated since 3.2.0
	 */
	public function test_papi_get_page() {
		$page = papi_get_page( $this->post_id );
		$this->assertTrue( is_object( $page ) );
		$page = papi_get_page( $this->post_id, 'fake' );
		$this->assertNull( $page );
	}
}
