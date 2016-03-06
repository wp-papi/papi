<?php

/**
 * Abstract implementation of Papi Porter driver.
 */
abstract class Papi_Porter_Driver {

	/**
	 * The driver name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Alias for driver name.
	 *
	 * @var array|string
	 */
	protected $alias = [];

	/**
	 * Options per property.
	 *
	 * @var array
	 */
	protected $options = [
		'custom'    => [],
		'meta_id'   => 0,
		'meta_type' => 'post',
		'property'  => null,
		'slug'      => '',
		'value'     => null
	];

	/**
	 * Papi Porter instance.
	 *
	 * @var Papi_Porter
	 */
	protected $porter;

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
	 * @param  mixed $value
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
	 * @param  string $type
	 * @param  string $filter
	 *
	 * @return string
	 */
	public function filter( $type, $filter ) {
		if ( ! is_string( $filter ) ) {
			$filter = '';
		}

		return sprintf(
			'papi/porter/driver/%s/%s/%s',
			$this->name,
			$type,
			$filter
		);
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
	 * Set driver alias.
	 */
	protected function set_driver_alias() {
		foreach ( papi_to_array( $this->alias ) as $alias ) {
			if ( is_string( $alias ) && ! $this->porter->exists( 'driver.' . $alias ) ) {
				$this->set_driver_name( $alias );
			}
		}
	}

	/**
	 * Set driver name.
	 *
	 * @param  string $name
	 *
	 * @throws InvalidArgumentException if driver name is empty or not a string.
	 * @throws Exception if driver name exists.
	 */
	protected function set_driver_name( $name ) {
		if ( empty( $name ) || ! is_string( $name ) ) {
			throw new InvalidArgumentException(
				'Driver name is empty or not a string.'
			);
		}

		$name = strtolower( $name );

		if ( $this->porter->exists( 'driver.' . $name ) ) {
			throw new Exception( sprintf( '`%s` driver exists.', $name ) );
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
		$this->set_driver_alias();
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
	 * @param  string $slug
	 *
	 * @return bool
	 */
	protected function should_update_array( $slug ) {
		return is_string( $slug ) &&
			isset( $this->options['custom'] ) &&
			isset( $this->options['custom'][$slug] ) &&
			$this->options['custom'][$slug]['update_array'];
	}

	/**
	 * Return the given object. Useful for chaining.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  mixed $obj
	 *
	 * @return mixed
	 */
	protected function with( $obj ) {
		return $obj;
	}
}
