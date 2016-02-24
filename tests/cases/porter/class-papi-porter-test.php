<?php

class Papi_Porter_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->porter = new Papi_Porter();

		if ( ! class_exists( 'Papi_Porter_Driver_Core2' ) ) {
			require_once PAPI_FIXTURE_DIR . '/porter/class-papi-porter-driver-core2.php';
		}

		if ( ! class_exists( 'Papi_Porter_Driver_Fail' ) ) {
			require_once PAPI_FIXTURE_DIR . '/porter/class-papi-porter-driver-fail.php';
		}
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->porter
		);
	}

	public function test_add_driver() {
		try {
			$this->assertSame( $this->porter, $this->porter->add_driver(
				new Papi_Porter_Driver_Core
			) );
		} catch ( Exception $e ) {
			$this->assertSame( '`core` driver exists.', $e->getMessage() );
		}

		$this->assertSame( $this->porter, $this->porter->add_driver(
			new Papi_Porter_Driver_Core2
		) );

		try {
			$this->assertSame( $this->porter, $this->porter->add_driver(
				new Papi_Porter_Driver_Core2
			) );
		} catch ( Exception $e ) {
			$this->assertSame( '`core2` driver exists.', $e->getMessage() );
		}

		try {
			$this->assertSame( $this->porter, $this->porter->add_driver(
				new Papi_Porter_Driver_Fail
			) );
		} catch ( Exception $e ) {
			$this->assertSame( 'Driver name is empty or not a string.', $e->getMessage() );
		}
	}

	public function test_export() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, papi_get_page_type_key(), 'properties-page-type' );

		$this->assertEmpty( $this->porter->export( 0 ) );
		$output = $this->porter->export( $post_id );
		$this->assertSame( 'bool', $output['Properties']['bool_test']->type );
		$output = $this->porter->export( $post_id, true );
		$this->assertFalse( is_object( $output['Properties']['bool_test'] ) );
		$this->assertNull( $output['Properties']['bool_test'] );

		papi_update_field( $post_id, 'checkbox_test', ['#000000'] );
		$this->assertSame( ['#000000'], papi_get_field( $post_id, 'checkbox_test' ) );

		$output = $this->porter->export( $post_id );
		$this->assertSame( ['#000000'], $output['Properties']['checkbox_test']->value );
		$output = $this->porter->export( $post_id, true );
		$this->assertFalse( is_object( $output['Properties']['checkbox_test'] ) );
		$this->assertSame( ['#000000'], $output['Properties']['checkbox_test'] );
	}

	public function test_filters() {
		$this->porter->before( 'driver:value', function ( $value, $slug ) {
			$this->assertSame( 'bool_test', $slug );
			return 'before';
		} );

		$this->porter->after( 'driver:value', function ( $value, $slug ) {
			$this->assertSame( 'bool_test', $slug );
			$this->assertFalse( $value );
			return true;
		} );

		$this->porter->options( [
			'bool_test' => [
				'update_array' => true
			]
		] );

		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, papi_get_page_type_key(), 'properties-page-type' );

		$output = $this->porter->import( $post_id, [
			'bool_test' => true
		] );

		$this->assertTrue( $output );
		$this->assertTrue( papi_get_field( $post_id, 'bool_test' ) );
	}

	public function test_fire_filter() {
		try {
			$this->porter->fire_filter( [] );
		} catch ( Exception $e ) {
			$this->assertSame( 'Missing `filter` in options array.', $e->getMessage() );
		}

		try {
			$this->porter->fire_filter( [
				'filter' => 'driver:test'
			] );
		} catch ( Exception $e ) {
			$this->assertSame( 'Missing `value` in options array.', $e->getMessage() );
		}

		tests_add_filter( 'papi/porter/driver/after/driver:test', function ( $value, $slug ) {
			$this->assertTrue( $value );
			$this->assertSame( 'bool_test', $slug );
		}, 10, 2 );

		tests_add_filter( 'papi/porter/driver/after/', function ( $value, $slug ) {
			$this->assertTrue( $value );
			$this->assertSame( 'bool_test', $slug );
		}, 10, 2 );

		$this->porter->fire_filter( [
			'filter' => [],
			'value'  => [
				true,
				'bool_test'
			]
		] );
	}

	public function test_import() {
		$this->assertFalse( $this->porter->import( null ) );

		$post_id = $this->factory->post->create();

		$this->assertFalse( $this->porter->import( $post_id, [] ) );
		$this->assertFalse( $this->porter->import( [
			'post_id'       => $post_id,
			'page_type'     => 'options/header-option-type',
			'update_arrays' => true
		] ) );

		update_post_meta( $post_id, papi_get_page_type_key(), 'properties-page-type' );

		$output = $this->porter->import( $post_id, [
			'fake_slug' => true,
			null
		] );

		$this->assertFalse( $output );

		$post_id = $this->factory->post->create();

		$output = $this->porter->import( [
			'post_id'   => $post_id,
			'page_type' => 'Fake_Page_Type'
			], [
			'bool_test' => true
			] );

		$this->assertFalse( $output );
	}

	public function test_use_driver() {
		try {
			$this->porter->use_driver( [] );
		} catch ( Exception $e ) {
			$this->assertSame( 'Invalid argument. Must be string.', $e->getMessage() );
		}

		try {
			$this->porter->use_driver( 'hello' );
		} catch ( Exception $e ) {
			$this->assertSame( '`hello` driver does not exist.', $e->getMessage() );
		}

		try {
			$this->porter->driver( 'hello' );
		} catch ( Exception $e ) {
			$this->assertSame( '`hello` driver does not exist.', $e->getMessage() );
		}

		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, papi_get_page_type_key(), 'properties-page-type' );

		if ( ! $this->porter->driver_exists( 'core2' ) ) {
			$this->assertSame( $this->porter, $this->porter->add_driver(
				new Papi_Porter_Driver_Core2
			) );
		}

		$output = $this->porter->use_driver( 'core2' )->import( $post_id, [
			'bool_test' => true
		] );

		$this->assertTrue( $output );
		$this->assertTrue( papi_get_field( $post_id, 'bool_test' ) );

		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, papi_get_page_type_key(), 'properties-page-type' );

		$output = $this->porter->driver( 'papi' )->import( $post_id, [
			'bool_test' => true
		] );

		$this->assertTrue( $output );
		$this->assertTrue( papi_get_field( $post_id, 'bool_test' ) );
	}
}
