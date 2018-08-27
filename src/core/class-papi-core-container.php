<?php

/**
 * Container class that implements a container.
 */
class Papi_Core_Container implements ArrayAccess {

	/**
	 * The classes holder.
	 *
	 * @var array
	 */
	protected $classes = [];

	/**
	 * The keys holder.
	 *
	 * @var array
	 */
	protected $keys = [];

	/**
	 * The values holder.
	 *
	 * @var array
	 */
	protected $values = [];

	/**
	 * Set a parameter or an object.
	 *
	 * @param string $id
	 * @param mixed  $value
	 * @param bool   $singleton
	 *
	 * @throws Exception when singleton cannot be rebind.
	 *
	 * @return mixed
	 */
	public function bind( $id, $value = null, $singleton = false ) {
		if ( is_string( $id ) && $this->is_singleton( $id ) ) {
			throw new Exception( sprintf( 'Identifier `%s` is a singleton and cannot be rebind', $id ) );
		}

		if ( is_object( $id ) && get_class( $id ) !== false ) {
			$value              = $id;
			$id                 = $this->get_class_prefix( get_class( $id ), false );
			$this->classes[$id] = true;
		}

		if ( $value instanceof Closure ) {
			$closure = $value;
		} else {
			$closure = $this->get_closure( $value, $singleton );
		}

		$this->values[$id] = compact( 'closure', 'singleton' );
		$this->keys[$id]   = true;

		return $value;
	}

	/**
	 * Call closure.
	 *
	 * @param mixed $closure
	 * @param array $parameters
	 *
	 * @return Closure
	 */
	protected function call_closure( $closure, array $parameters = [] ) {
		if ( $closure instanceof Closure ) {
			$rc      = new ReflectionFunction( $closure );
			$args    = $rc->getParameters();
			$params  = $parameters;
			$classes = [
				$this->get_class_prefix( get_class( $this ) ),
				get_class( $this ),
				get_parent_class( $this )
			];

			foreach ( $args as $index => $arg ) {
				if ( $arg->getClass() === null ) {
					continue;
				}

				if ( in_array( $arg->getClass()->name, $classes, true ) ) {
					$parameters[$index] = $this;
				} else if ( $this->exists( $arg->getClass()->name ) ) {
					$parameters[$index] = $this->make( $arg->getClass()->name );
				}
			}

			if ( ! empty( $args ) && empty( $parameters ) ) {
				$parameters[0] = $this;
			}

			if ( count( $args ) > count( $parameters ) ) {
				$parameters = array_merge( $parameters, $params );
			}

			return $this->call_closure( call_user_func_array( $closure, $parameters ), $parameters );
		}

		return $closure;
	}

	/**
	 * Check if identifier is set or not.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function exists( $id ) {
		return isset( $this->keys[$this->get_class_prefix( $id )] );
	}

	/**
	 * Get closure function.
	 *
	 * @param mixed $value
	 * @param bool  $singleton
	 *
	 * @return Closure
	 */
	protected function get_closure( $value, $singleton = false ) {
		return function () use ( $value, $singleton ) {
			return $value;
		};
	}

	/**
	 * Get class prefix.
	 *
	 * @param string $id
	 * @param bool   $check
	 *
	 * @return string
	 */
	protected function get_class_prefix( $id, $check = true ) {
		if ( strpos( $id, '\\' ) !== false && $id[0] !== '\\' ) {
			$class = '\\' . $id;

			if ( $check ) {
				return isset( $this->classes[$class] ) ? $class : $id;
			}

			return $class;
		}

		return $id;
	}

	/**
	 * Determine if a given type is a singleton or not.
	 *
	 * @param string $id
	 *
	 * @throws InvalidArgumentException if argument is not string.
	 *
	 * @return bool
	 */
	public function is_singleton( $id ) {
		if ( ! is_string( $id ) ) {
			throw new InvalidArgumentException( 'Invalid argument. Must be string.' );
		}

		if ( ! $this->exists( $id ) ) {
			return false;
		}

		$id = $this->get_class_prefix( $id );

		return $this->values[$id]['singleton'] === true;
	}

	/**
	 * Resolve the given type from the container.
	 *
	 * @param string $id
	 * @param array  $parameters
	 *
	 * @throws Exception if identifier is not defined.
	 *
	 * @return Closure
	 */
	public function make( $id, array $parameters = [] ) {
		if ( ! $this->exists( $id ) ) {
			throw new Exception( sprintf( 'Identifier `%s` is not defined', $id ) );
		}

		$id      = $this->get_class_prefix( $id );
		$value   = $this->values[$id];
		$closure = $value['closure'];

		return $this->call_closure( $closure, $parameters );
	}

	/**
	 * Add key and value to the container once.
	 *
	 * @param  string $key
	 * @param  mixed  $callback
	 *
	 * @return mixed
	 */
	public function once( $key, $callback ) {
		if ( ! is_string( $key ) && ! is_callable( $callback ) ) {
			return;
		}

		if ( ! $this->exists( $key ) ) {
			$result = $callback();
			$this->singleton( $key, $result );
		}

		return $this->make( $key );
	}

	/**
	 * Unset value by identifier.
	 *
	 * @param string $id
	 */
	public function remove( $id ) {
		$id = $this->get_class_prefix( $id );
		unset( $this->keys[$id], $this->values[$id] );
	}

	/**
	 * Reset container values.
	 */
	public function reset() {
		unset( $this->keys, $this->values, $this->classes );
	}

	/**
	 * Set a parameter or an object.
	 *
	 * @param string $id
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public function singleton( $id, $value = null ) {
		return $this->bind( $id, $value, true );
	}

	/**
	 * Check if identifier is set or not.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function offsetExists( $id ) {
		return $this->exists( $id );
	}

	/**
	 * Get value by identifier.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function offsetGet( $id ) {
		return $this->make( $id );
	}

	/**
	 * Set a parameter or an object.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $id
	 * @param mixed  $value
	 */
	public function offsetSet( $id, $value ) {
		$this->bind( $id, $value );
	}

	/**
	 * Unset value by identifier.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $id
	 */
	public function offsetUnset( $id ) {
		$this->remove( $id );
	}
}
