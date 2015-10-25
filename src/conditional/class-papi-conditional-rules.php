<?php

/**
 * Conditional rules class that contains
 * all rules.
 */
class Papi_Conditional_Rules {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_filters();
	}

	/**
	 * Convert string bool to bool.
	 *
	 * @param  mixed $str
	 *
	 * @return mixed
	 */
	private function convert_bool( $str ) {
		if ( ! is_string( $str ) ) {
			return $str;
		}

		switch ( $str ) {
			case 'false':
				return false;
			case 'true':
				return true;
			default:
				return $str;
		}
	}

	/**
	 * Convert value from a property.
	 *
	 * @param  mixed $value
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return mixed
	 */
	private function convert_prop( $value, Papi_Core_Conditional_Rule $rule ) {
		$post_id   = papi_get_post_id();
		$page_type = papi_get_page_type_by_post_id( $post_id );

		if ( ! papi_is_empty( $value ) && $page_type instanceof Papi_Page_Type !== false ) {
			$property = $page_type->get_property( $rule->slug );

			if ( papi_is_property( $property ) ) {
				$prop_value = $property->format_value(
					$value,
					$property->slug,
					$post_id
				);

				$prop_value = papi_filter_format_value(
					$property->type,
					$prop_value,
					$property->slug,
					$post_id
				);

				$prop_value = $this->get_deep_value(
					$rule->slug,
					$prop_value
				);

				if ( gettype( $prop_value ) === gettype( $rule->value ) ) {
					return $prop_value;
				}
			}

			return $value;
		}

		return $value;
	}

	/**
	 * Convert string number to int or float.
	 *
	 * @param  string $str
	 *
	 * @return float|int
	 */
	private function convert_number( $str ) {
		if ( is_numeric( $str ) && ! is_string( $str ) || ! is_numeric( $str ) ) {
			return $str;
		}

		if ( $str == (int) $str ) {
			return (int) $str;
		} else {
			return (float) $str;
		}
	}

	/**
	 * Get converted value.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return array
	 */
	private function get_converted_value( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule );

		// Convert numeric values.
		if ( is_numeric( $value ) && is_numeric( $rule->value ) ) {
			return [
				$this->convert_number( $value ),
				$this->convert_number( $rule->value )
			];
		}

		// Convert bool value if it a string bool or return value.
		$value       = $this->convert_bool( $value );
		$rule->value = $this->convert_bool( $rule->value );

		// Try to convert the property to the same value as the rule value.
		return [
			$this->convert_prop( $value, $rule ),
			$rule->value
		];
	}

	/**
	 * Get deep value.
	 *
	 * @param  string $slug
	 * @param  mixed $value
	 *
	 * @return mixed
	 */
	private function get_deep_value( $slug, $value ) {
		$slugs = explode( '.', $slug );
		array_shift( $slugs );
		return papi_field_value( $slugs, $value, $value );
	}

	/**
	 * Get property value.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return mixed
	 */
	private function get_value( Papi_Core_Conditional_Rule $rule ) {
		if ( papi_doing_ajax() ) {
			$source    = $rule->get_source();
			$post_id   = papi_get_post_id();
			$page_type = papi_get_page_type_by_post_id( $post_id );

			if ( ! papi_is_empty( $source ) && $page_type instanceof Papi_Page_Type !== false ) {
				if ( papi_is_property( $page_type->get_property( $rule->slug ) ) ) {
					return $this->get_deep_value( $rule->slug, $source );
				}
			}
		}

		if ( ! papi_is_empty( $rule->get_source() ) ) {
			return $this->get_deep_value( $rule->slug, $rule->get_source() );
		}

		$slug = $rule->get_field_slug();

		if ( papi_is_option_page() ) {
			$value = papi_get_option( $slug );
		} else {
		 	$value = papi_get_field( $slug );
		}

		return $this->get_deep_value( $slug, $value );
	}

	/**
	 * Equal conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_equal( Papi_Core_Conditional_Rule $rule ) {
		list( $value, $rule_value ) = $this->get_converted_value( $rule );
		return $value === $rule_value;
	}

	/**
	 * Not equal conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_not_equal( Papi_Core_Conditional_Rule $rule ) {
		list( $value, $rule_value ) = $this->get_converted_value( $rule );
		return $value !== $rule_value;
	}

	/**
	 * Greater then conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_greater_then( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule );

		if ( is_array( $value ) ) {
			$value = count( $value );
		}

		if ( ! is_numeric( $value ) || ! is_numeric( $rule->value ) ) {
			return false;
		}

		return $this->convert_number( $value ) >
			$this->convert_number( $rule->value );
	}

	/**
	 * Greater then or equal conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_greater_then_or_equal( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule );

		if ( is_array( $value ) ) {
			$value = count( $value );
		}

		if ( ! is_numeric( $value ) || ! is_numeric( $rule->value ) ) {
			return false;
		}

		return $this->convert_number( $value ) >=
			$this->convert_number( $rule->value );
	}

	/**
	 * Less then conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_less_then( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule );

		if ( is_array( $value ) ) {
			$value = count( $value );
		}

		if ( ! is_numeric( $value ) || ! is_numeric( $rule->value ) ) {
			return false;
		}

		return $this->convert_number( $value ) <
			$this->convert_number( $rule->value );
	}

	/**
	 * Less then or equal conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_less_then_or_equal( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule );

		if ( is_array( $value ) ) {
			$value = count( $value );
		}

		if ( ! is_numeric( $value ) || ! is_numeric( $rule->value ) ) {
			return false;
		}

		return $this->convert_number( $value ) <=
			$this->convert_number( $rule->value );
	}

	/**
	 * In array conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_in( Papi_Core_Conditional_Rule $rule ) {
		list( $value, $rule_value ) = $this->get_converted_value( $rule );

		if ( ! is_array( $rule_value ) ) {
			return false;
		}

		return in_array( $value, $rule_value );
	}

	/**
	 * Not in array conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_not_in( Papi_Core_Conditional_Rule $rule ) {
		list( $value, $rule_value ) = $this->get_converted_value( $rule );

		if ( ! is_array( $rule_value ) ) {
			return false;
		}

		return ! in_array( $value, $rule_value );
	}

	/**
	 * Like conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_like( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule );

		if ( ! is_string( $value ) ) {
			$value = papi_convert_to_string( $value );
		}

		if ( papi_is_empty( $value ) ) {
			return false;
		}

		return strpos(
			strtolower( $value ),
			strtolower( $rule->value )
		) !== false;
	}

	/**
	 * Get between values.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return array
	 */
	private function get_between_values( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule );

		if ( ! is_array( $rule->value ) ) {
			return [$rule, false];
		}

		foreach ( $rule->value as $index => $v ) {
			$v = $this->convert_number( $v );

			if ( is_numeric( $v ) ) {
				$rule->value[$index] = $v;
			} else {
				unset( $rule->value[$index] );
			}
		}

		if ( ! is_numeric( $value ) || count( $rule->value ) !== 2 ) {
			return [$rule, false];
		}

		return [$rule, $this->convert_number( $value )];
	}

	/**
	 * Between conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_between( Papi_Core_Conditional_Rule $rule ) {
		list( $rule, $value ) = $this->get_between_values( $rule );

		if ( $value === false ) {
			return false;
		}

		return $rule->value[0] <= $value && $value <= $rule->value[1];
	}

	/**
	 * Not between conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_not_between( Papi_Core_Conditional_Rule $rule ) {
		list( $rule, $value ) = $this->get_between_values( $rule );

		if ( $value === false ) {
			return false;
		}

		return ! ( $rule->value[0] <= $value && $value <= $rule->value[1] );
	}

	/**
	 * Exists conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_exists( Papi_Core_Conditional_Rule $rule ) {
		return ! in_array( $this->get_value( $rule ), [null, []] );
	}

	/**
	 * Not exists conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_not_exists( Papi_Core_Conditional_Rule $rule ) {
		return in_array( $this->get_value( $rule ), [null, []] );
	}

	/**
	 * Empty conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_empty( Papi_Core_Conditional_Rule $rule ) {
		return papi_is_empty( $this->get_value( $rule ) );
	}

	/**
	 * Empty conditional rule.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */
	public function rule_not_empty( Papi_Core_Conditional_Rule $rule ) {
		return ! papi_is_empty( $this->get_value( $rule ) );
	}

	/**
	 * Setup filters.
	 */
	public function setup_filters() {
		add_filter( 'papi/conditional/rule/=', [$this, 'rule_equal'] );
		add_filter( 'papi/conditional/rule/!=', [$this, 'rule_not_equal'] );
		add_filter( 'papi/conditional/rule/>', [$this, 'rule_greater_then'] );
		add_filter( 'papi/conditional/rule/>=', [$this, 'rule_greater_then_or_equal'] );
		add_filter( 'papi/conditional/rule/<', [$this, 'rule_less_then'] );
		add_filter( 'papi/conditional/rule/<=', [$this, 'rule_less_then_or_equal'] );
		add_filter( 'papi/conditional/rule/IN', [$this, 'rule_in'] );
		add_filter( 'papi/conditional/rule/NOT IN', [$this, 'rule_not_in'] );
		add_filter( 'papi/conditional/rule/LIKE', [$this, 'rule_like'] );
		add_filter( 'papi/conditional/rule/BETWEEN', [$this, 'rule_between'] );
		add_filter( 'papi/conditional/rule/NOT BETWEEN', [$this, 'rule_not_between'] );
		add_filter( 'papi/conditional/rule/EXISTS', [$this, 'rule_exists'] );
		add_filter( 'papi/conditional/rule/NOT EXISTS', [$this, 'rule_not_exists'] );
		add_filter( 'papi/conditional/rule/EMPTY', [$this, 'rule_empty'] );
		add_filter( 'papi/conditional/rule/NOT EMPTY', [$this, 'rule_not_empty'] );
	}
}

new Papi_Conditional_Rules();
