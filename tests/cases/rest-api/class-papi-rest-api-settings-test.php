<?php

/**
 * @group rest-api
 */
class Papi_REST_API_Settings_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		if ( version_compare( get_bloginfo( 'version' ), '4.7', '<' ) ) {
			$this->markTestSkipped( '`register_settings` is only supported in WordPress 4.7 and later' );
		}

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types/options';
		} );

		$this->class = new Papi_REST_API_Settings;
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->class );
	}

	public function test_actions() {
		$this->assertSame( 10, has_action( 'rest_api_init', [$this->class, 'register'] ) );
		$this->assertSame( 10, has_filter( 'rest_pre_get_setting', [$this->class, 'pre_get_setting'] ) );
	}

	public function test_pre_get_setting() {
		$this->assertFalse( has_filter( 'rest_request_after_callbacks', [$this->class, 'prepare_response'] ) );
		$this->class->pre_get_setting( null );
		$this->assertSame( 10, has_filter( 'rest_request_after_callbacks', [$this->class, 'prepare_response'] ) );
	}

	public function test_get_setting() {
		$this->assertNull( $this->class->get_setting( 'name', null ) );

		update_option( 'name', 'Fredrik' );
		$this->class->register();

		$this->assertSame( 'Fredrik', $this->class->get_setting( 'name', null ) );
	}

	public function test_prepare_response() {
		$response = [
			'name' => null
		];

		$response = $this->class->prepare_response( $response );
		$this->assertSame( ['name' => null], $response );

		$response = [
			'name' => null
		];

		$this->class->register();
		$response = $this->class->prepare_response( $response );
		$this->assertSame( ['name' => null], $response );

		$response = [
			'name' => null
		];

		update_option( 'name', 'Fredrik' );
		$this->class->register();
		$response = $this->class->prepare_response( $response );
		$this->assertSame( ['name' => 'Fredrik'], $response );
	}
}
