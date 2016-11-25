<?php

/**
 * Core class that implements a conditional rule.
 */
class Papi_Core_Conditional_Rule {

	/**
	 * The operator.
	 *
	 * @var string
	 */
	public $operator = '=';

	/**
	 * The slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * The source value.
	 *
	 * @var mixed
	 */
	public $source;

	/**
	 * The value.
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * The constructor.
	 *
	 * @param array $rule
	 */
	public function __construct( array $rule ) {
		$this->setup( $rule );
	}

	/**
	 * Get field slug.
	 *
	 * @return string
	 */
	public function get_field_slug() {
		if ( preg_match( '/\[|\]/', $this->slug ) ) {
			$slug = preg_replace( '/\[|\]/', '.', $this->slug );
			$slug = str_replace( '..', '.', $slug );
			return substr( $slug, 0, -1 );
		}

		return $this->slug;
	}

	/**
	 * Get the source value.
	 *
	 * @return mixed
	 */
	public function get_source() {
		if ( is_callable( $this->source ) ) {
			return call_user_func_array( $this->source, [$this->slug] );
		}

		if ( is_string( $this->source ) && strpos( $this->source, '#' ) !== false ) {
			$source = explode( '#', $this->source );

			if ( empty( $source[0] ) || empty( $source[1] ) ) {
				return $this->source;
			}

			$source[0] = new $source[0]();

			if ( method_exists( $source[0], $source[1] ) ) {
				return call_user_func_array( $source, [$this->slug] );
			}

			return;
		}

		return $this->source;
	}

	/**
	 * Setup source callable.
	 *
	 * @param  string $value
	 *
	 * @return string
	 */
	public function setup_source( $value ) {
		if ( is_array( $value ) && count( $value ) === 2 && is_object( $value[0] ) && is_string( $value[1] ) ) {
			return sprintf( '%s#%s', get_class( $value[0] ), $value[1] );
		}

		if ( is_string( $value ) && is_callable( $value ) ) {
			return $value;
		}

		// No support for closure.
		if ( is_object( $value ) && $value instanceof Closure ) {
			return '';
		}

		return $value;
	}

	/**
	 * Setup the rule and assign properties with values.
	 *
	 * @param array  $rule
	 */
	protected function setup( array $rule ) {
		foreach ( $rule as $key => $value ) {
			if ( $key === 'operator' ) {
				$value = strtoupper( $value );
				$value = html_entity_decode( $value );
			} else if ( $key === 'slug' ) {
				$value = papify( $value );
			} else if ( $key === 'source' ) {
				$value = $this->setup_source( $value );
			}

			$this->$key = $value;
		}
	}
}
