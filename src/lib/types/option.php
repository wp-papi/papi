<?php

/**
 * Check if it's a option page url.
 *
 * @return bool
 */
function papi_is_option_page() {
	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url  = parse_url( $request_uri );

	if ( ! isset( $parsed_url['query'] ) || empty( $parsed_url['query'] ) ) {
		return false;
	}

	$query = $parsed_url['query'];

	return is_admin()
		&& ! preg_match( '/page\-type\=/', $query )
		&& preg_match( '/page\=papi/', $query );
}

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
	$option_types = papi_get_all_content_types( [
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
