<?php

abstract class Papi_Property_Test_Case extends WP_UnitTestCase {

	/**
	 * Setup test and add global properties, post id, slugs and so.
	 */
	public function setUp() {
		parent::setUp();

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types', PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$this->meta_type = 'post';
		$this->post_id   = $this->factory->post->create();
		$this->term_id   = $this->factory->term->create();

		$_GET = [];
		$_GET['post']    = $this->post_id;
		$_GET['term_id'] = $this->term_id;

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'properties-page-type' );
		update_term_meta( $this->term_id, papi_get_page_type_key(), 'properties-taxonomy-type' );

		$this->entry_type = papi_get_entry_type_by_id( 'properties-page-type' );
		$this->entry_type = papi_get_entry_type_by_id( 'properties-taxonomy-type' );

		if ( isset( $this->slug ) && is_string( $this->slug ) ) {
			$this->slugs      = [$this->slug];
			$this->property   = $this->entry_type->get_property( $this->slug );
			$this->properties = [$this->property];
		} else {
			$slugs = $this->slugs;
			$this->properties = [];
			foreach ( $slugs as $slug ) {
				$this->properties[] = $this->entry_type->get_property( $slug );
			}
		}
	}

	/**
	 * Tear down all properties that has been used.
	 */
	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $_POST, $this->post_id, $this->term_id, $this->entry_type, $this->property );
	}

	/**
	 * Get property value.
	 *
	 * @param  string $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( $name === 'property' && count( $this->properties ) > 0 ) {
			return $this->properties[0];
		}

		if ( $name === 'slug' && count( $this->slugs ) > 0 ) {
			return $this->slugs[0];
		}

		return parent::__get( $name );
	}

	/**
	 * Assert values will assert values so it's the same by default.
	 */
	public function assert_values( $expected, $actual, $slug ) {
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Get actual value for the given slug.
	 */
	abstract public function get_value();

	/**
	 * Get expected value for the given slug.
	 */
	abstract public function get_expected();

	/**
	 * Save properties value for different types.
	 *
	 * @param  Papi_Property $property
	 * @param  mixed  $value
	 * @param  string $type
	 */
	public function save_properties( $property, $value = null, $type = 'post' ) {
		global $wp_current_filter;

		if ( is_null( $value ) ) {
			$value = $this->get_value( $property->get_slug( true ) );
		}

		$values = papi_test_create_property_post_data( [
			'slug'  => $property->slug,
			'type'  => $property,
			'value' => $value
		] );

		foreach ( $values as $key => $value ) {
			if ( ! isset( $_POST[$key] ) ) {
				$_POST[$key] = $value;
			}
		}

		$_POST['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );

		switch ( papi_get_meta_type( $type ) ) {
			case 'post':
				$id = $this->post_id;
				$handler = new Papi_Admin_Meta_Handler();
				$wp_current_filter = ['save_post'];
				break;
			case 'term':
				$id = $this->term_id;
				$handler = new Papi_Admin_Meta_Handler();
				$wp_current_filter = ['create_term'];
				break;
			case 'option':
				$_SERVER['REQUEST_METHOD'] = 'POST';
				$id = 0;
				$handler = new Papi_Admin_Option_Handler();
				$wp_current_filter = [];
				break;
			default:
				$id = 0;
				break;
		}

		$handler->save_properties( $id );
		$_SERVER['REQUEST_METHOD'] = '';
		$wp_current_filter = [];
	}

	/**
	 * Save properties value for the different meta types.
	 *
	 * @param  Papi_Property $property
	 * @param  string $type
	 */
	public function save_properties_value( $property = null, $type = 'post' ) {
		$_GET['meta_type'] = $type;

		$this->meta_type = $type;

		// Set right entry type for meta type.
		switch ( $type ) {
			case 'option':
				global $current_screen;

				$current_screen = WP_Screen::get( 'admin_init' );

				$_GET['page'] = 'papi/option/options/properties-option-type';
				$_SERVER['REQUEST_URI'] = 'http://site.com/?page=papi/option/options/properties-option-type';

				break;
			case 'term':
				$_GET['entry_type'] = 'properties-taxonomy-type';
				break;
			case 'post':
				$_GET['entry_type'] = 'properties-page-type';
				break;
			default:
				break;
		}

		$value = $this->get_value( $property->get_slug( true ) );

		if ( is_null( $value ) ) {
			switch ( $type ) {
				case 'option':
					$actual = papi_get_option( $property->slug );
					break;
				case 'post':
					$actual = papi_get_field( $this->post_id, $property->slug );
					break;
				case 'term':
					$actual = papi_get_term_field( $this->term_id, $property->slug );
					break;
				default:
					break;
			}
			$this->assertNull( $actual );
			return;
		}

		$this->save_properties( $property, $value, $type );

		switch ( $type ) {
			case 'option':
				// Leave admin screen.
				$current_screen = null;
				$actual = papi_get_option( $property->slug );
				unset( $_GET['page'] );
				break;
			case 'post':
				$actual = papi_get_field( $this->post_id, $property->slug );
				break;
			case 'term':
				$actual = papi_get_term_field( $this->term_id, $property->slug );
				break;
			default:
				break;
		}

		$expected = $this->get_expected( $property->get_slug( true ) );

		$this->assert_values( $expected, $actual, $property->get_slug( true ) );
	}

	/**
	 * Test property convert type, by default it is `string` if isn't set to anything else.
	 */
	public function test_property_convert_type() {
		foreach ( $this->properties as $property ) {
			$this->assertSame( 'string', $property->convert_type );
		}
	}

	/**
	 * Test property default value, by default is `null` if it isn't set to anything else.
	 */
	public function test_property_default_value() {
		foreach ( $this->properties as $property ) {
			$this->assertNull( $property->default_value );
		}
	}

	/**
	 * Test property `format_value`, it will check so the
	 * expected value is return by default.
	 */
	public function test_property_format_value() {
		$this->assertSame( $this->get_expected(), $this->property->format_value( $this->get_value(), '', 0 ) );
	}

	/**
	 * Test properties `get_default_settings` so it is a array.
	 */
	public function test_property_get_default_settings() {
		foreach ( $this->properties as $property ) {
			$this->assertTrue( is_array( $property->get_default_settings() ) );
		}
	}

	/**
	 * Test property `import_value`, it will check so the
	 * expected value is return by default.
	 */
	public function test_property_import_value() {
		$this->assertSame( $this->get_expected(), $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	/**
	 * Abstract method that should test property options, if any.
	 */
	abstract public function test_property_options();

	/**
	 * Test property output will test so the html contains the property.
	 */
	public function test_property_output() {
		foreach ( $this->properties as $property ) {
			papi_render_property( $property );
			$this->expectOutputRegex( '/name=\"' . papi_get_property_type_key( $property->get_option( 'slug' ) ) . '\"' );
			$this->expectOutputRegex( '/data\-property=\"' . $property->get_option( 'type' ) . '\"/' );
		}
	}

	/**
	 * Test save properties will test to save properties as
	 * all existing types.
	 */
	public function test_save_properties_value() {
		foreach ( $this->properties as $prop ) {
			$this->save_properties_value( $prop, 'option' );
			$this->save_properties_value( $prop, 'post' );
			$this->save_properties_value( $prop, 'term' );
		}

		$_SERVER['REQUEST_URI'] = '';
	}

	/**
	 * Get update value will return the value that will
	 * be used as value when updating the property.
	 *
	 * @return mixed
	 */
	public function get_update_value() {
		return;
	}

	/**
	 * Update type value will test to update the property value.
	 *
	 * @param  int    $post_id
	 * @param  int    $index
	 * @param  string $slug
	 * @param  string $type
	 */
	public function update_type_value( $post_id = 0, $index = 0, $slug = null, $type = 'post' ) {
		$property = $this->properties[$index];
		$value    = $this->get_update_value( $property->get_slug( true ) );

		if ( is_null( $value ) ) {
			$this->assertNull( papi_get_field( $this->post_id, $property->slug, null, $type ) );
		} else if ( papi_update_field( $this->post_id, $slug, $value, $type ) ) {
			$actual = papi_get_field( $this->post_id, $property->slug, null, $type );
			$this->assert_values( $value, $actual, $property->slug );
		}
	}

	/**
	 * Test so values is updated right.
	 *
	 * @depends test_save_properties_value
	 */
	public function test_property_update_type_value() {
		foreach ( $this->slugs as $index => $slug ) {
			$this->update_type_value( $this->post_id, $index, $slug );
			$this->update_type_value( 0, $index, $slug, 'option' );
		}
	}
}
