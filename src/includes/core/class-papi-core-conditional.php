<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Core Conditional.
 *
 * @package Papi
 */

class Papi_Core_Conditional {

	/**
	 * Available relations.
	 *
	 * @var array
	 */

	private $relations = [
		'AND',
		'OR'
	];

	/**
	 * Check if the property should be displayed by the rules.
	 *
	 * @param  array $rules
	 *
	 * @return bool
	 */

	public function display( array $rules ) {
		if ( empty( $rules ) ) {
			return true;
		}

		if ( ! isset( $rules['relation'] ) ) {
			$rules['relation'] = 'OR';
		} else {
			$rules['relation'] = strtoupper( $rules['relation'] );
		}

		if ( in_array( $rules['relation'], $this->relations ) ) {
			return $this->display_by_relation( $rules );
		}

		return true;
	}

	/**
	 * Get rule class.
	 *
	 * @param array $rule
	 *
	 * @return Papi_Core_Conditional_Rule|null
	 */

	private function get_rule( $rule ) {
		if ( ! is_array( $rule ) ) {
			return;
		}

		return new Papi_Core_Conditional_Rule( $rule );
	}

	/**
	 * Get the display by relation.
	 *
	 * @param array $rules
	 *
	 * @return bool
	 */

	private function display_by_relation( array $rules ) {
		if ( $rules['relation'] === 'AND' ) {
			$display = true;

			foreach ( $rules as $rule ) {
				if ( ! $display ) {
					break;
				}

				if ( $rule = $this->get_rule( $rule ) ) {
					if ( $result = $this->call_rule( $rule ) ) {
						$display = true;
					}
				}
			}

			return $display;
		} else if ( $rules['relation'] === 'OR' ) {
			$display = true;

			foreach ( $rules as $rule ) {
				if ( $rule = $this->get_rule( $rule ) ) {
					$result = $this->call_rule( $rule );
					$display = $display ? $result : $display;
				}
			}

			return $display;
		}

		return false;
	}

	/**
	 * Call rule.
	 *
	 * @param Papi_Core_Conditional_Rule $rule
	 *
	 * @return bool
	 */

	private function call_rule( $rule ) {
		$result =  apply_filters( 'papi/conditional/rule/' . strtoupper( $rule->operator ), $rule );

		if ( $result === true || $result === false ) {
			return $result;
		}

		return false;
	}
}
