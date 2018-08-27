<?php

class Papi_Lib_Core_Data_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_data_delete() {
		$this->assertFalse( papi_data_delete( $this->post_id, 'name' ) );

		papi_data_update( $this->post_id, 'name', 'Fredrik' );
		$this->assertTrue( papi_data_delete( $this->post_id, 'name' ) );
	}

	public function test_papi_data_get() {
		$this->assertEmpty( papi_data_get( $this->post_id, 'name' ) );

		papi_data_update( $this->post_id, 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', papi_data_get( $this->post_id, 'name' ) );
	}

	public function test_papi_data_update() {
		$this->assertEmpty( papi_data_get( $this->post_id, 'name' ) );

		papi_data_update( $this->post_id, 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', papi_data_get( $this->post_id, 'name' ) );
	}
}
