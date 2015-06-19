<?php

abstract class Papi_Property_Test_Case extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		$_GET = [];
		$_GET['post'] = $this->post_id;

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'properties-page-type' );

		$this->page_type = papi_get_page_type_by_id( 'properties-page-type' );
		$this->property  = $this->page_type->get_property( $this->slug );

	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$_GET,
			$_POST,
			$this->post_id,
			$this->page_type,
			$this->property
		);
	}

	public function test_convert_type() {
		$this->assertEquals( 'string', $this->property->convert_type );
	}

	public function test_default_value() {
		$this->assertNull( $this->property->default_value );
	}

	public function test_output() {
		papi_render_property( $this->property );
		$this->expectOutputRegex( '/name=\"' . papi_get_property_type_key( $this->property->get_option( 'slug' ) ) . '\"' );
		$this->expectOutputRegex( '/data\-property=\"' . $this->property->get_option( 'type' ) . '\"/' );
	}

	public function test_save_property_value() {
		$value = $this->get_value();

		if ( is_null( $value ) ) {
			$this->assertNull( papi_get_field( $this->post_id, $this->property->slug ) );
			return;
		}

		$handler = new Papi_Admin_Post_Handler();

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => $value
		], $_POST );

		$handler->save_property( $this->post_id );

		$actual = papi_get_field( $this->post_id, $this->property->slug );

		$expected = $this->get_expected();

		$this->assertEquals( $expected, $actual );
	}

	abstract public function test_property_options();

	abstract public function get_value();

	abstract public function get_expected();

}
