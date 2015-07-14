<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Core Conditional class.
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

		$rules = $this->prepare_rules( $rules );

		if ( in_array( $rules['relation'], $this->relations ) ) {
			return $this->display_by_relation( $rules );
		}

		return true;
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

				if ( $rule instanceof Papi_Core_Conditional_Rule ) {
					$display = papi_filter_conditional_rule_allowed( $rule );
				}
			}

			return $display;
		}

		$empty = array_filter( $rules, function ( $rule ) {
			return $rule instanceof Papi_Core_Conditional_Rule ? true : null;
		} );

		if ( empty( $empty ) ) {
			return true;
		}

		$result = [];

		foreach ( $rules as $rule ) {
			if ( $rule instanceof Papi_Core_Conditional_Rule ) {
				$result[] = papi_filter_conditional_rule_allowed( $rule );
			}
		}

		$result = array_filter( $result, function ( $res ) {
			return $res === true ? true : null;
		} );

		return ! empty( $result );
	}

	/**
	 * Prepare rules.
	 *
	 * @param array $rules
	 *
	 * @return array
	 */

	public function prepare_rules( array $rules ) {
		if ( ! isset( $rules['relation'] ) ) {
			$rules['relation'] = 'OR';
		} else {
			$rules['relation'] = strtoupper( $rules['relation'] );
		}

		foreach ( $rules as $index => $value ) {
			if ( is_string( $index ) ) {
				continue;
			}

			if ( is_array( $value ) && isset( $value['slug'] ) ) {
				$value['slug'] = papify( $value['slug'] );
			}

			$rules[$index] = new Papi_Core_Conditional_Rule( $value );
		}

		return $rules;
	}
}
