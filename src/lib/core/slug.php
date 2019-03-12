<?php

/**
 * Modify slug via core filter.
 *
 * @param  string $slug
 *
 * @return string
 */
function papi_filter_slug( $slug ) {
	return apply_filters( 'papi/slug', $slug );
}

/**
 * Prefix slug. Handles slugs with papi prefix.
 *
 * @param  string $prefix
 * @param  string $slug
 *
 * @return string
 */
function papi_prefix_slug( $prefix, $slug ) {
	$papify = preg_match( '/papi\_/', $slug );
	$slug = unpapify( $slug );

	if ( strpos( $slug, $prefix ) !== 0 ) {
		$slug = implode( '_', [$prefix, $slug] );
	}

	if ( $papify ) {
		$slug = papify( $slug );
	}

	return $slug;
}

/**
 * Add `papi_` to the given string ad the start of the string.
 *
 * @param  string $str
 *
 * @return string
 */
function papify( $str = '' ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	if ( ! preg_match( '/^\_\_papi|^\_papi|^papi\_/', $str ) ) {
		return str_replace( 'papi__', 'papi_', 'papi_' . $str );
	}

	return $str;
}

/**
 * Remove `papi-` or `papi_` from the given string.
 *
 * @param  string $str
 *
 * @return string
 */
function unpapify( $str ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	return str_replace( 'papi-', '', str_replace( 'papi_', '', $str ) );
}
