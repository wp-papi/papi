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

		if ( isset( $this->slug ) && is_string( $this->slug ) ) {
			$this->property   = $this->page_type->get_property( $this->slug );
			$this->properties = [$this->property];
		} else {
			$slugs = $this->slugs;
			$this->properties = [];
			foreach ( $slugs as $slug ) {
				$this->properties[] = $this->page_type->get_property( $slug );
			}
		}
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

	abstract public function get_value();

	abstract public function get_expected();

	public function save_properties( $property, $value = null ) {
		if ( is_null( $value ) ) {
			$value = $this->get_value( $property->get_slug( true ) );
		}

		$handler = new Papi_Admin_Post_Handler();

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->slug,
			'type'  => $property,
			'value' => $value
		], $_POST );

		$handler->save_properties( $this->post_id );
	}

	public function save_properties_value( $property = null ) {
		$value = $this->get_value( $property->get_slug( true ) );

		if ( is_null( $value ) ) {
			$this->assertNull( papi_get_field( $this->post_id, $property->slug ) );
			return;
		}

		$this->save_properties( $property, $value );

		$actual = papi_get_field( $this->post_id, $property->slug );

		$expected = $this->get_expected( $property->get_slug( true ) );

		$this->assertEquals( $expected, $actual );
	}

	public function test_property_convert_type() {
		foreach ( $this->properties as $property ) {
			$this->assertEquals( 'string', $property->convert_type );
		}
	}

	public function test_property_default_value() {
		foreach ( $this->properties as $property ) {
			$this->assertNull( $property->default_value );
		}
	}

	public function test_property_format_value() {
		$this->assertEquals( $this->get_expected(), $this->property->format_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_get_default_settings() {
		foreach ( $this->properties as $property ) {
			$this->assertTrue( is_array( $property->get_default_settings() ) );
		}
	}

	public function test_property_import_value() {
		$this->assertEquals( $this->get_expected(), $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	abstract public function test_property_options();

	public function test_property_output() {
		foreach ( $this->properties as $property ) {
			papi_render_property( $property );
			$this->expectOutputRegex( '/name=\"' . papi_get_property_type_key( $property->get_option( 'slug' ) ) . '\"' );
			$this->expectOutputRegex( '/data\-property=\"' . $property->get_option( 'type' ) . '\"/' );
		}
	}

	public function test_save_properties_value() {
		foreach ( $this->properties as $prop ) {
			$this->save_properties_value( $prop );
		}
	}
}
