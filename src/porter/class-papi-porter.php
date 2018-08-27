<?php

/**
 * Papi Porter class that handle import and export
 * of properties data.
 */
final class Papi_Porter extends Papi_Core_Container {

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
		$this->use_driver( 'core' );
		$this->driver->bootstrap();
	}

	/**
	 * Add Porter Driver.
	 *
	 * @param  Papi_Porter_Driver $driver
	 *
	 * @return Papi_Porter
	 */
	public function add_driver( Papi_Porter_Driver $driver ) {
		$driver->set_porter( $this );

		return $this;
	}

	/**
	 * Add after filter.
	 *
	 * @param  string  $filter
	 * @param  Closure $closure
	 * @param  int     $priority
	 * @param  int     $accepted_args
	 *
	 * @return bool
	 */
	public function after( $filter, Closure $closure, $priority = 10, $accepted_args = 2 ) {
		$filter = $this->driver->filter( 'after', $filter );

		return add_filter( $filter, $closure, $priority, $accepted_args );
	}

	/**
	 * Add before filter.
	 *
	 * @param  string  $filter
	 * @param  Closure $closure
	 * @param  int     $priority
	 * @param  int     $accepted_args
	 *
	 * @return bool
	 */
	public function before( $filter, Closure $closure, $priority = 10, $accepted_args = 2 ) {
		$filter = $this->driver->filter( 'before', $filter );

		return add_filter( $filter, $closure, $priority, $accepted_args );
	}

	/**
	 * Alias for `add_driver` or `use_driver` method.
	 *
	 * @param  string|Papi_Porter_Driver $driver
	 *
	 * @return Papi_Porter
	 */
	public function driver( $driver ) {
		return $driver instanceof Papi_Porter_Driver ? $this->add_driver( $driver ) : $this->use_driver( $driver );
	}

	/**
	 * Check if a driver exists or not.
	 *
	 * @param  string $driver
	 *
	 * @return bool
	 */
	public function driver_exists( $driver ) {
		return is_string( $driver ) && $this->exists( 'driver.' . $driver );
	}

	/**
	 * Export data from Papi. With or without all property options.
	 *
	 * @param  mixed $post_id
	 * @param  bool  $only_values
	 *
	 * @return array
	 */
	public function export( $post_id, $only_values = false ) {
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
					$store = papi_get_meta_store( $post_id );

					// @codeCoverageIgnoreStart
					if ( is_null( $store ) ) {
						continue;
					}
					// @codeCoverageIgnoreEnd

					$property = $store->get_property( $slug );

					// @codeCoverageIgnoreStart
					if ( ! papi_is_property( $property ) ) {
						continue;
					}
					// @codeCoverageIgnoreEnd

					$options            = clone $property->get_options();
					$options->value     = $value;
					$slugs[$key][$slug] = $options;
				}
			}
		}

		return $slugs;
	}

	/**
	 * Fire filter.
	 *
	 * @param  array $options
	 *
	 * @throws Exception if `filter` is missing from options array.
	 * @throws Exception if `value` is missing from options array.
	 *
	 * @return mixed
	 */
	public function fire_filter( array $options ) {
		if ( ! isset( $options['type'] ) ) {
			$options['type'] = 'after';
		}

		if ( ! isset( $options['filter'] ) ) {
			throw new Exception( 'Missing `filter` in options array.' );
		}

		if ( ! isset( $options['value'] ) ) {
			throw new Exception( 'Missing `value` in options array.' );
		}

		$arguments = [
			$this->driver->filter( $options['type'], $options['filter'] ),
		];

		$value = $options['value'];

		foreach ( papi_to_array( $value ) as $val ) {
			$arguments[] = $val;
		}

		return call_user_func_array( 'apply_filters', $arguments );
	}

	/**
	 * Get import options.
	 *
	 * @param  mixed $options
	 *
	 * @return array
	 */
	protected function get_import_options( $options ) {
		$default_options = [
			'meta_id'       => 0,
			'meta_type'     => 'post',
			'post_id'       => 0,
			'page_type'     => '',
			'update_arrays' => false
		];

		if ( ! is_array( $options ) ) {
			$options = array_merge( $default_options, [
				'post_id' => papi_get_post_id( $options )
			] );
		}

		return array_merge( $default_options, $options );
	}

	/**
	 * Get value that should be saved.
	 *
	 * @param  array $options
	 *
	 * @return mixed
	 */
	protected function get_value( array $options ) {
		return $this->driver->get_value( $options );
	}

	/**
	 * Import data to Papi.
	 *
	 * @param  mixed $options
	 * @param  array $fields
	 *
	 * @return bool
	 */
	public function import( $options, array $fields = [] ) {
		$options    = $this->get_import_options( $options );
		$meta_id    = empty( $options['meta_id'] ) ? $options['post_id'] : $options['meta_id'];
		$meta_type  = $options['meta_type'];
		$entry_type = $options['page_type'];

		if ( isset( $options['update_arrays'] ) ) {
			$this->driver->set_options( [
				'update_array' => $options['update_arrays']
			] );
		}

		if ( empty( $meta_id ) || empty( $fields ) ) {
			return false;
		}

		if ( empty( $entry_type ) ) {
			$entry_type = papi_get_entry_type_by_meta_id( $meta_id, $meta_type );
		}

		if ( is_string( $entry_type ) ) {
			$entry_type = papi_get_entry_type_by_id( $entry_type );
		}

		if ( $entry_type instanceof Papi_Entry_Type === false ) {
			return false;
		}

		update_metadata( $meta_type, $meta_id, papi_get_page_type_key(), $entry_type->get_id() );

		$result = true;

		foreach ( $fields as $slug => $value ) {
			if ( ! is_string( $slug ) || papi_is_empty( $value ) ) {
				continue;
			}

			$property = $entry_type->get_property( $slug );

			if ( ! papi_is_property( $property ) ) {
				$result = false;
				continue;
			}

			$value = $this->fire_filter( [
				'filter' => 'driver:value',
				'type'   => 'before',
				'value'  => [$value, $slug]
			] );

			$value = $this->get_value( [
				'post_id'  => $meta_id,
				'property' => $property,
				'slug'     => $slug,
				'value'    => $value
			] );

			$value = $this->fire_filter( [
				'filter' => 'driver:value',
				'type'   => 'after',
				'value'  => [$value, $slug]
			] );

			$out = papi_data_update( $meta_id, $slug, $value, $meta_type );

			$result = $out ? $result : $out;
		}

		return $result;
	}

	/**
	 * Add options per property.
	 *
	 * @param  array $options
	 *
	 * @return Papi_Porter
	 */
	public function options( array $options = [] ) {
		$this->driver->set_options( [
			'custom' => $options
		] );

		return $this;
	}

	/**
	 * Change Porter driver.
	 *
	 * @param  string $driver
	 *
	 * @throws InvalidArgumentException if an argument is not of the expected type.
	 * @throws Exception if driver name does not exist.
	 *
	 * @return Papi_Porter
	 */
	public function use_driver( $driver ) {
		if ( ! is_string( $driver ) ) {
			throw new InvalidArgumentException( 'Invalid argument. Must be string.' );
		}

		$driver = strtolower( $driver );

		if ( ! $this->exists( 'driver.' . $driver ) ) {
			throw new Exception( sprintf( '`%s` driver does not exist.', $driver ) );
		}

		$class = $this->make( 'driver.' . $driver );

		if ( class_exists( $class ) ) {
			$this->driver = new $class( $this );
			$this->driver->bootstrap();
		}

		return $this;
	}
}
