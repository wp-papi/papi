<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Conditional Rules class.
 *
 * @package Papi
 */

class Papi_Conditional_Rules {

	/**
	 * The constructor.
	 */

	public function __construct() {
		$this->setup_filters();
	}

	/**
	 * Convert string number to int or float.
	 *
	 * @param string $str
	 *
	 * @return float|int
	 */

	private function convert_number( $str ) {
		if ( is_numeric( $str ) && is_string( $str ) ) {
			return $str;
		}

		if ( $str == (int) $str ) {
			return (int) $str;
		} else {
			return (float) $str;
		}
	}

	/**
	 * Get property value.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */

	private function get_value( $slug ) {
		if ( papi_is_option_page() ) {
			return papi_get_option( $slug );
		}

		return papi_get_field( $slug );
	}

	/**
	 * Equal conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_equal( Papi_Core_Conditional_Rule $rule ) {
		return $rule->value === $this->get_value( $rule->slug );
	}

	/**
	 * Not equal conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_not_equal( Papi_Core_Conditional_Rule $rule ) {
		return $rule->value !== $this->get_value( $rule->slug );
	}

	/**
	 * Greater then conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_greater_then( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule->slug );

		if ( ! is_numeric( $value ) ) {
			return false;
		}

		return $this->convert_number( $value ) > $rule->value;
	}

	/**
	 * Greater then or equal conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_greater_then_or_equal( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule->slug );

		if ( ! is_numeric( $value ) ) {
			return false;
		}

		return $this->convert_number( $value ) >= $rule->value;
	}

	/**
	 * Less then conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_less_then( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule->slug );

		if ( ! is_numeric( $value ) ) {
			return false;
		}

		return $this->convert_number( $value ) < $rule->value;
	}

	/**
	 * Less then or equal conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_less_then_or_equal( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule->slug );

		if ( ! is_numeric( $value ) ) {
			return false;
		}

		return $this->convert_number( $value ) <= $rule->value;
	}

	/**
	 * In array conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_in( Papi_Core_Conditional_Rule $rule ) {
		$arr = $rule->value;

		if ( ! is_array( $arr ) ) {
			return false;
		}

		return in_array( $this->get_value( $rule->slug ), $arr );
	}

	/**
	 * Not in array conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_not_in( Papi_Core_Conditional_Rule $rule ) {
		$arr = $rule->value;

		if ( ! is_array( $arr ) ) {
			return false;
		}

		return ! in_array( $this->get_value( $rule->slug ), $arr );
	}

	/**
	 * Like conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_like( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule->slug );

		if ( ! is_string( $value ) ) {
			$value = papi_convert_to_string( $value );
		}

		if ( papi_is_empty ( $value ) ) {
			return false;
		}

		return strpos( strtolower( $value ), strtolower( $rule->value ) ) !== false;
	}

	/**
	 * Between conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_between( Papi_Core_Conditional_Rule $rule ) {
		$value = $this->get_value( $rule->slug );

		if ( ! is_numeric( $value ) || ! is_array( $rule->value ) || count( $rule->value ) !== 2 ) {
			return false;
		}

		$value = $this->convert_number( $value );

		return $rule->value[0] <= $value && $value <= $rule->value[1];
	}

	/**
	 * Not exists conditional rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	public function rule_not_exists( Papi_Core_Conditional_Rule $rule ) {
		return $this->get_value( $rule->slug ) === null;
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
		add_filter( 'papi/conditional/rule/NOT EXISTS', [$this, 'rule_not_exists'] );
	}
}

new Papi_Conditional_Rules();
