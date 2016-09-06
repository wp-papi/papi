<?php

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
