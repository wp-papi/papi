<?php

/**
 * Check if the given value is a instance of the rule class.
 *
 * @param  mixed $rule
 *
 * @return bool
 */
function papi_is_rule( $rule ) {
	return $rule instanceof Papi_Core_Conditional_Rule;
}

/**
 * Get conditional rule.
 *
 * @param  array|Papi_Core_Conditional_Rule $rule
 *
 * @return Papi_Core_Conditional_Rule
 */
function papi_rule( $rule ) {
	if ( is_array( $rule ) && ! empty( $rule ) ) {
		return new Papi_Core_Conditional_Rule( $rule );
	}

	if ( $rule instanceof Papi_Core_Conditional_Rule ) {
		return $rule;
	}
}
