<?php

use Tank\Container;

/**
 * Papi Porter class.
 *
 * @package Papi
 */
class Papi_Porter extends Container {

    /**
     * The driver that should be used.
     *
     * @var Papi_Porter_Driver
     */
    protected $driver;

    /**
     * The constructor.
     */
    public function __construct() {
        $this->add_driver( new Papi_Porter_Driver_Core );
        $this->driver( 'core' );
        $this->driver->bootstrap();
    }

    /**
     * Add Porter Driver.
     *
     * @param Papi_Porter_Driver $driver
     *
     * @return Papi_Porter
     */
    public function add_driver( Papi_Porter_Driver $driver ) {
        $driver->set_porter( $this );
        return $this;
    }

    /**
     * Export data from Papi. With or without all property options.
     *
     * @param mixed $post_id
     * @param bool $only_values
     *
     * @return array
     */
    public function export( $post_id = null, $only_values = false ) {
        $post_id = papi_get_post_id( $post_id );

        if ( empty( $post_id ) ) {
            return [];
        }

        $slugs = papi_get_slugs( $post_id );

        foreach ( $slugs as $key => $box ) {
            foreach ( $box as $index => $slug ) {
                unset( $slugs[$key][$index] );
                $value = papi_get_field( $post_id, $slug, null );

                if ( $only_values === true ) {
                    $slugs[$key][$slug] = $value;
                } else {
                    $page = papi_get_page( $post_id );

                	if ( is_null( $page ) ) {
                		continue;
                	}

                	$property = $page->get_property( $slug );

                	if ( ! papi_is_property( $property ) ) {
                		continue;
                	}

                    $options = clone $property->get_options();
                    $options->value = $value;

                    $slugs[$key][$slug] = $options;
                }
            }
        }

        return $slugs;
    }

    /**
     * Change porter driver.
     *
     * @param  Papi_Porter_Driver $driver
     *
     * @throws InvalidArgumentException if an argument is not of the expected type.
     * @throws Exception if driver name does not exist.
     * @throws Exception if driver class does not exist.
     *
     * @return Papi_Porter
     */
    public function driver( $driver ) {
		if ( ! is_string( $driver ) ) {
			throw new InvalidArgumentException( 'Invalid argument. Must be string.' );
		}

        $driver = strtolower( $driver );

        if ( ! $this->exists( 'driver.' . $driver ) ) {
            throw new Exception( sprintf( '`%s` driver does not exist.', $driver ) );
        }

        $class = $this->make( 'driver.' . $driver );

        if ( ! class_exists( $class ) ) {
            throw new Exception( sprintf( '`%s` driver class does not exist.', $class ) );
        }

        $this->driver = new $class( $this );
        $this->driver->bootstrap();

        return $this;
    }

    /**
     * Get import options.
     *
     * @param mixed $options
     *
     * @return array
     */
    protected function get_import_options( $options ) {
        $default_options = [
            'post_id'       => 0,
            'page_type'     => '',
            'update_arrays' => false
        ];

        if ( ! is_array( $options ) ) {
            $options = array_merge( $default_options, [
                'post_id' => papi_get_post_id( $options )
            ] );
        }

        return $options;
    }

    /**
     * Get value that should be saved.
     *
     * @param array $options
     *
     * @return mixed
     */
    protected function get_value( array $options ) {
        $value = $this->driver->get_value( $options );

        if ( $this->exists( $slug ) ) {
            $value = $this->make( $slug, [$value] );
        }

        return $value;
    }

    /**
     * Import data to Papi.
     *
     * @param array $options
     * @param array $fields
     *
     * @return bool
     */
    public function import( $options, array $fields = [] ) {
        $options   = $this->get_import_options( $options );
        $post_id   = $options['post_id'];
        $page_type = $options['page_type'];

        if ( $updated_all_arrays = $options['update_all_arrays'] ) {
            $this->driver->set_options( [
                'update_array' => $update_all_arrays
            ] );
        }

        if ( empty( $post_id ) || empty( $fields ) ) {
            return false;
        }

        if ( empty( $page_type ) ) {
            $page_type = papi_get_page_type_by_post_id( $options['post_id'] );
        }

        if ( is_string( $page_type ) ) {
            $page_type = papi_get_page_type_by_id( $page_type );
        }

        if ( ! papi_is_page_type( $page_type ) ) {
            return false;
        }

        $result = true;

        foreach ( $fields as $slug => $value ) {
            if ( ! is_string( $slug ) || is_null( $value ) ) {
                continue;
            }

            $property = $page_type->get_property( $slug );

            if ( ! papi_is_property( $property ) ) {
                $result = false;
                continue;
            }

            $value = $this->get_value( [
                'post_id'  => $post_id,
                'property' => $property,
                'slug'     => $slug,
                'value'    => $value
            ] );

            $out = papi_update_property_meta_value( [
                'post_id' => $post_id,
                'slug'    => $slug,
                'value'   => $value
            ] );

            $result = $out ? $result : $out;
        }

        return $result;
    }

    /**
     * Add options per property.
     *
     * @param array $options
     *
     * @return Papi_Porter
     */
    public function options( array $options = [] ) {
        $this->driver->set_options( $options );
        return $this;
    }

    /**
     * Add special cases for field.
     *
     * @param string $slug
     * @param Closure $closure
     */
    public function with( $slug, $closure ) {
        return $this->bind( $slug, $closure );
    }

}
