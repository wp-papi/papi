<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Porter_Driver_Core` class.
 *
 * @package Papi
 */
class Papi_Porter_Driver_Core_Test extends WP_UnitTestCase {

    public function setUp() {
        parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

        $this->driver    = new Papi_Porter_Driver_Core();
		$this->post_id   = $this->factory->post->create();
        $this->page_type = papi_get_page_type_by_id( 'properties-page-type' );

        update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'properties-page-type' );
    }

    public function tearDown() {
        parent::tearDown();
        unset(
            $this->driver,
            $this->post_id,
            $this->page_type
        );
    }

    public function test_bootstrap() {
        $this->assertNull( $this->driver->bootstrap() );
    }

    public function test_get_driver_name() {
        $this->assertEquals( 'core', $this->driver->get_driver_name() );
    }

    public function test_get_options() {
        $expected = [
            'custom'   => [],
            'post_id'  => 0,
            'property' => null,
            'slug'     => '',
            'value'    => null
        ];

        $this->assertEquals( $expected, $this->driver->get_options() );
    }

    public function test_get_value() {
        try {
            $this->driver->get_value( [] );
        } catch ( InvalidArgumentException $e ) {
            $this->assertEquals( 'Missing `post_id` option. Should be int.', $e->getMessage() );
        }

        try {
            $this->driver->get_value( [
                'post_id' => $this->post_id
            ] );
        } catch ( InvalidArgumentException $e ) {
            $this->assertEquals( 'Missing `property` option. Should be instance of `Papi_Core_Property`.', $e->getMessage() );
        }

        try {
            $this->driver->get_value( [
                'post_id'  => $this->post_id,
                'property' => $this->page_type->get_property( 'bool_test' )
            ] );
        } catch ( InvalidArgumentException $e ) {
            $this->assertEquals( 'Missing `value` option.', $e->getMessage() );
        }

        $output = $this->driver->get_value( [
            'post_id'  => $this->post_id,
            'property' => $this->page_type->get_property( 'bool_test' ),
            'value'    => true
        ] );

        $this->assertTrue( $output );

        $output = $this->driver->get_value( [
            'post_id'  => $this->post_id,
            'property' => $this->page_type->get_property( 'bool_test' ),
            'value'    => function () {
                return true;
            }
        ] );

        $this->assertTrue( $output );

        $this->driver->set_options( [
            'custom' => [
                'bool_test' => [
                    'update_array' => true
                ]
            ]
        ] );

        $output = $this->driver->get_value( [
            'post_id'  => $this->post_id,
            'property' => $this->page_type->get_property( 'bool_test' ),
            'value'    => function () {
                return true;
            }
        ] );

        $this->assertTrue( $output );
    }

    public function test_get_value_with_array() {
        papi_update_field( $this->post_id, 'checkbox_test', ['#000000'] );
        $this->assertEquals( ['#000000'], papi_get_field( $this->post_id, 'checkbox_test' ) );

        $this->driver->set_options( [
            'custom' => [
                'checkbox_test' => [
                    'update_array' => true
                ]
            ]
        ] );

        $output = $this->driver->get_value( [
            'post_id'  => $this->post_id,
            'property' => $this->page_type->get_property( 'checkbox_test' ),
            'value'    => [
                '#ffffff'
            ]
        ] );

        $this->assertEquals( ['#000000', '#ffffff'], $output );
        papi_update_field( $this->post_id, 'checkbox_test',  $output );
        $this->assertEquals( ['#000000', '#ffffff'], papi_get_field( $this->post_id, 'checkbox_test' ) );
    }

    public function test_set_options() {
        $this->driver->set_options( [
            'test' => true
        ] );

        $options = $this->driver->get_options();

        $this->assertTrue( $options['test'] );
    }

}
