<?php

/**
 * @group core
 */
class Papi_Core_Data_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();
		$this->term_id = $this->factory->term->create();

		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->post_id, $this->term_id );
	}

	public function test_delete() {
		$data = new Papi_Core_Data();

		$this->assertFalse( $data->delete( $this->post_id, 'random223-page-type' ) );

		update_post_meta( $this->post_id, 'random223-page-type', 'post' );
		$this->assertTrue( $data->delete( $this->post_id, 'random223-page-type' ) );

		update_post_meta( $this->post_id, 'random223-page-type', 'post' );
		$this->assertTrue( $data->delete( $this->post_id, 'papi_random223-page-type' ) );
	}

	public function test_delete_option() {
		$data = new Papi_Core_Data( 'option' );

		$this->assertFalse( $data->delete( $this->post_id, 'random223-page-type' ) );

		update_option( 'random223-page-type', 'option' );
		$this->assertTrue( $data->delete( $this->post_id, 'random223-page-type' ) );

		update_option( 'random223-page-type', 'option' );
		$this->assertTrue( $data->delete( $this->post_id, 'papi_random223-page-type' ) );
	}

	public function test_get() {
		$data = new Papi_Core_Data();

		$this->assertEmpty( $data->get( $this->post_id, 'name' ) );

		update_post_meta( $this->post_id, 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $data->get( $this->post_id, 'name' ) );
	}

	public function test_get_option() {
		$data = new Papi_Core_Data( 'option' );

		$this->assertEmpty( $data->get( $this->post_id, 'name' ) );

		update_option( 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $data->get( $this->post_id, 'name' ) );
	}

	public function test_get_term() {
		$data = new Papi_Core_Data( 'term' );

		$this->assertEmpty( $data->get( $this->term_id, 'name' ) );

		update_term_meta( $this->term_id, 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $data->get( $this->term_id, 'name' ) );
	}

	public function test_update() {
		foreach ( ['post', 'option', 'term'] as $type ) {
			$id = $type === 'term' ? $this->term_id : $this->post_id;

			$data = new Papi_Core_Data( $type );

			$this->assertTrue( $data->update( $id, 'name', 'Fredrik' ) );
			$this->assertFalse( $data->update( $id, 'name', 'Fredrik' ) );
			$this->assertSame( 'Fredrik', $data->get( $id, 'name' ) );

			$this->assertTrue( $data->update( $id, 'name', '' ) );
			$this->assertEmpty( $data->get( $id, 'name' ) );

			$this->assertTrue( $data->update( $id, 'what', [
				'firstname' => 'Fredrik'
			] ) );
			$this->assertSame( 'Fredrik', $data->get( $id, 'firstname' ) );

			$this->assertTrue( $data->update( $id, 'what', [
				'Fredrik'
			] ) );
			$this->assertSame( ['Fredrik'], $data->get( $id, 'what' ) );

			$this->assertTrue( $data->update( $id, 'what', '{}' ) );
			$this->assertEmpty( $data->get( $id, 'what' ) );
		}
	}
}
