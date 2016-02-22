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
	return wp_cache_delete( papi_cache_key( $key, $suffix, $type ) );
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
	return wp_cache_get( papi_cache_key( $key, $suffix, $type ) );
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

	$type = $type === 'page' ? 'post' : $type;

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
	return wp_cache_set( papi_cache_key( $key, $suffix, $type ), $value );
}
