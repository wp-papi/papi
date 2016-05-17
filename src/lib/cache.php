<?php

/**
 * Delete value from cache.
 *
 * @param  string $key
 * @param  mixed  $suffix
 *
 * @return bool
 */
function papi_cache_delete( $key, $suffix ) {
	$key = papi_cache_key( $key, $suffix );
	$out = true;

	if ( is_admin() ) {
		$out = wp_cache_delete( 'admin_' . $key );
	}

	return $out ? wp_cache_delete( $key ) : $out;
}

/**
 * Get value from cache.
 *
 * @param  string $key
 * @param  mixed  $suffix
 *
 * @return bool
 */
function papi_cache_get( $key, $suffix ) {
	$key = papi_cache_key( $key, $suffix );

	if ( is_admin() ) {
		$key = 'admin_' . $key;
	}

	return wp_cache_get( $key );
}

/**
 * Get Papi cache key.
 *
 * @param  string $key
 * @param  mixed  $suffix
 *
 * @return string
 */
function papi_cache_key( $key, $suffix ) {
	if ( ! is_string( $key ) ) {
		return '';
	}

	$key    = papify( $key );
	$suffix = papi_convert_to_string( $suffix );
	$suffix = papi_html_name( $suffix );
	$suffix = papi_remove_papi( $suffix );

	return sprintf( '%s_%s', $key, $suffix );
}

/**
 * Set value in cache.
 *
 * @param  string $key
 * @param  mixed  $suffix
 * @param  mixed  $value
 *
 * @return bool
 */
function papi_cache_set( $key, $suffix, $value ) {
	$key = papi_cache_key( $key, $suffix );

	if ( is_admin() ) {
		$key = 'admin_' . $key;
	}

	return wp_cache_set( $key, $value );
}
