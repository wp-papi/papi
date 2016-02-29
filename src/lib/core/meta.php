<?php

/**
 * Get right meta id column for a meta type.
 *
 * @param  string $type
 *
 * @return string|null
 */
function papi_get_meta_id_column( $type = 'post' ) {
	if ( $type = papi_get_meta_type( $type ) ) {
		return sprintf( '%s_id', $type );
	}
}

/**
 * Get the meta store.
 *
 * @param  int    $post_id
 * @param  string $type
 *
 * @return Papi_Core_Meta_Store|null
 */
function papi_get_meta_store( $post_id = 0, $type = 'post' ) {
	return Papi_Core_Meta_Store::factory( $post_id, $type );
}

/**
 * Get right meta type value. It will treat option
 * as a meta type even if isn't a real meta type.
 *
 * @param  string $type
 *
 * @return string|null
 */
function papi_get_meta_type( $type = null ) {
	switch ( $type ) {
		case 'option':
			return 'option';
		case 'post':
		case 'page':
			return 'post';
		case 'taxonomy':
		case 'term':
			return 'term';
		default:
			break;
	}

	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url  = parse_url( $request_uri );

	if ( ! empty( $parsed_url['query'] ) ) {
		// Taxonomy page in admin.
		if ( is_admin() && preg_match( '/taxonomy=/', $parsed_url['query'] ) ) {
			return 'term';
		}

		// Option page in admin.
		if ( is_admin() && preg_match( '/page\=papi(\%2F|\/)option/', $parsed_url['query'] ) ) {
			return 'option';
		}
	}

	// Default was has to be set here since we trying to figure out
	// which url conform which meta type.
	if ( is_null( $type ) ) {
		$type = 'post';
	}

	// If meta type exists as a filter we can return it.
	if ( function_exists( "get_{$type}_meta" ) ) {
		return $type;
	}
}
