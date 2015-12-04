<?php

class Papi_Lib_Core_Deprecated_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET = [];

		add_action( 'deprecated_function_run', [$this, 'deprecated_function_run'] );
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
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

	public function test_deprecated() {
		$this->assertTrue( true );
	}
}
