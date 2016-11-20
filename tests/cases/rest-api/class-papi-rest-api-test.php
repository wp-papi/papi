<?php

/**
 * @group rest-api
 */
class Papi_REST_API_Test extends WP_UnitTestCase {

    public function setUp() {
        parent::setUp();
        
        $this->class = new Papi_REST_API;
    }

    public function tearDown() {
        parent::tearDown();

        unset( $this->class );
    }

	public function test_actions() {
		$this->assertSame( 10, has_action( 'rest_api_init', [$this->class, 'rest_api_init'] ) );
		$this->assertSame( 10, has_action( 'rest_api_init', [$this->class, 'register_routes'] ) );
	}
}