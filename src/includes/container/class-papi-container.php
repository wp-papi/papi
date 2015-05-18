<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Container.
 *
 * @package Papi
 * @since 1.2.0
 */

class Papi_Container implements \ArrayAccess {

	/**
	 * The keys holder.
	 *
	 * @var array
	 * @since 1.2.0
	 */

	protected $keys = [];

	/**
	 * The values holder.
	 *
	 * @var array
	 * @since 1.2.0
	 */

	protected $values = [];

	/**
	 * Set a parameter or an object.
	 *
	 * @param string $id
	 * @param mixed $value
	 */

	public function bind( $id, $value ) {
		if ( ! $value instanceof Closure ) {
			$value = function() use ( $value ) {
				return $value;
			};
		}

		$this->values[$id] = $value;
		$this->keys[$id] = true;
	}

	/**
	 * Check if identifier is set or not.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */

	public function exists( $id ) {
		return isset( $this->keys[$id] );
	}

	/**
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */

	public function make( $id ) {
		if ( ! isset( $this->keys[$id] ) ) {
			throw new \InvalidArgumentException( sprintf( 'Identifier [%s] is not defined', $id ) );
		}

		return $this->values[$id]();
	}

	/**
	 * Check if identifier is set or not.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */

	// @codingStandardsIgnoreStart
	public function offsetExists( $id ) {
	// @codingStandardsIgnoreEnd
		return $this->exists( $id );
	}

	/**
	 * Get value by identifier.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */

	// @codingStandardsIgnoreStart
	public function offsetGet( $id ) {
	// @codingStandardsIgnoreEnd
		return $this->make( $id );
	}

	/**
	 * Set a parameter or an object.
	 *
	 * @param string $id
	 * @param mixed $value
	 */

	// @codingStandardsIgnoreStart
	public function offsetSet( $id, $value ) {
	// @codingStandardsIgnoreEnd
		$this->bind( $id, $value );
	}

	/**
	 * Unset value by identifier.
	 *
	 * @param string $id
	 */

	// @codingStandardsIgnoreStart
	public function offsetUnset( $id ) {
	// @codingStandardsIgnoreEnd
		unset( $this->keys[$id], $this->values[$id] );
	}
}
