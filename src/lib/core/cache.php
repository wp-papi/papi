<?php

/**
 * Delete value from cache.
 *
 * @param  string $key
 * @param  mixed  $suffix
 * @param  string $type
 *
 * @return bool
 */
function papi_cache_delete( $key, $suffix, $type = 'post' ) {
	$key = papi_cache_key( $key, $suffix, $type );
	$out = true;

	if ( papi_is_admin() ) {
		$out = wp_cache_delete( 'admin_' . $key );
	}

	return $out ? wp_cache_delete( $key ) : $out;
}

/**
 * Get value from cache.
 *
 * @param  string $key
 * @param  mixed  $suffix
 * @param  string $type
 *
 * @return bool
 */
function papi_cache_get( $key, $suffix, $type = 'post' ) {
	$key = papi_cache_key( $key, $suffix, $type );

	if ( papi_is_admin() ) {
		$key = 'admin_' . $key;
	}

	return wp_cache_get( $key );
}

/**
 * Get Papi cache key.
 *
 * @param  string $key
 * @param  mixed  $suffix
 * @param  string $type
 *
 * @return string
 */
function papi_cache_key( $key, $suffix, $type = 'post' ) {
	if ( ! is_string( $key ) ) {
		return '';
	}

	$type   = empty( $type ) ? 'post' : $type;
	$type   = $type === 'page' ? 'post' : $type;
	$key    = unpapify( $key );
	$key    = papify( $type . '_' . $key );
	$suffix = papi_convert_to_string( $suffix );
	$suffix = papi_html_name( $suffix );
	$suffix = unpapify( $suffix );

	return sprintf( '%s_%s', $key, $suffix );
}

/**
 * Set value in cache.
 *
 * @param  string $key
 * @param  mixed  $suffix
 * @param  mixed  $value
 * @param  string $type
 *
 * @return bool
 */
function papi_cache_set( $key, $suffix, $value, $type = 'post' ) {
	$key = papi_cache_key( $key, $suffix, $type );

	if ( papi_is_admin() ) {
		$key = 'admin_' . $key;
	}

	return wp_cache_set( $key, $value );
}
