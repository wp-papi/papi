<?php

/**
 * Check if `$obj` is a instanceof `Papi_Option_Type`.
 *
 * @param  mixed $obj
 *
 * @return bool
 */
function papi_is_option_type( $obj ) {
	return $obj instanceof Papi_Option_Type;
}

/**
 * Check if option type exists.
 *
 * @param  string $id
 *
 * @return bool
 */
function papi_option_type_exists( $id ) {
	$exists       = false;
	$option_types = papi_get_all_entry_types( [
		'types' => 'option'
	] );

	foreach ( $option_types as $option_type ) {
		if ( $option_type->match_id( $id ) ) {
			$exists = true;
			break;
		}
	}

	return $exists;
}
