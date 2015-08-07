<?php

/**
 * Papi Porter Driver class.
 *
 * @package Papi
 */
abstract class Papi_Porter_Driver {

    /**
     * The driver name.
     *
     * @var string
     */
     protected $name = '';

    /**
     * Options per property.
     *
     * @var array
     */
    protected $options = [
        'custom'   => [],
        'post_id'  => 0,
        'property' => null,
        'slug'     => '',
        'value'    => null
    ];

    /**
     * Papi Porter instance.
     *
     * @var Papi_Porter
     */
    protected $porter;

    /**
     * The post id.
     *
     * @var int
     */
    protected $post_id;

    /**
     * The constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct() {
    }

    /**
     * Call closure value if any.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function call_value( $value ) {
        if ( $value instanceof Closure ) {
            return $value();
        }

        return $value;
    }

    /**
     * Bootstrap the driver.
     */
    public function bootstrap() {
    }

    /**
     * Get filter key.
     *
     * @param string $type
     * @param string $filter
     *
     * @return string
     */
    public function filter( $type, $filter ) {
        if ( ! is_string( $filter ) ) {
            $filter = '';
        }

        return sprintf( 'papi/porter/driver/%s/%s/%s', $this->name, $type, $filter );
    }

    /**
     * Get the driver name.
     *
     * @return string
     */
    public function get_driver_name() {
        return $this->name;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * Get the import value for a property.
     *
     * @param  array  $options
     *
     * @return mixed
     */
    abstract public function get_value( array $options );

    /**
     * Set driver name.
     *
     * @param string $name
     *
     * @throws InvalidArgumentException if an argument is not of the expected type.
     * @throws Exception if driver name is empty.
     * @throws Exception if driver name exists.
     */
    protected function set_driver_name( $name ) {
		if ( ! is_string( $name ) ) {
			throw new InvalidArgumentException( 'Invalid argument. Must be string.' );
		}

        if ( empty( $name ) ) {
            throw new Exception( 'Driver name is empty.' );
        }

        $name = strtolower( $name );

        if ( $this->porter->exists( 'driver.' . $name ) ) {
            throw new Exception( sprintf( '`%s` exists.', $name ) );
        }

        $this->porter->singleton( 'driver.' . $name, get_class( $this ) );
    }

    /**
     * Set Porter instance.
     *
     * @param Papi_Porter $porter
     */
    public function set_porter( Papi_Porter $porter ) {
        $this->porter = $porter;
        $this->set_driver_name( $this->name );
    }

    /**
     * Set options for properties.
     *
     * @param array $options
     */
    public function set_options( array $options = [] ) {
        $this->options = array_merge( $this->options, $options );
    }

    /**
     * Determine if a property should update existing array or not.
     *
     * @param string $slug
     *
     * @return bool
     */
    protected function should_update_array( $slug ) {
        if ( ! is_string( $slug ) ) {
            return false;
        }

        return isset( $this->options['custom'] ) &&
            isset( $this->options['custom'][$slug] ) &&
            $this->options['custom'][$slug]['update_array'];
    }

    /**
     * Return the given object. Useful for chaining.
     *
     * @param mixed $obj
     *
     * @return obj
     */
    protected function with( $obj ) {
        return $obj;
    }

}
