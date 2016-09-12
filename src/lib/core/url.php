<?php

/**
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param  string $page_type
 * @param  bool   $append_admin_url
 * @param  string $post_type
 * @param  array  $include
 *
 * @return string
 */
function papi_get_page_new_url( $page_type, $append_admin_url = true, $post_type = null, array $include = [] ) {
	// Prepare query strings.
	$include = empty( $include ) ? array_keys( $_GET ) : $include;
	$include = array_diff( $include, ['page', 'page_type', 'post_type'] );

	// Create new admin url.
	$admin_url = $append_admin_url ? get_admin_url() : '';
	$admin_url = $admin_url . 'post-new.php?page_type=' . $page_type . papi_include_query_strings( '&', $include );

	return papi_append_post_type_query( $admin_url, $post_type );
}

/**
 * Append post type query string.
 *
 * @param  string $url
 * @param  string $post_type_arg
 *
 * @return string
 */
function papi_append_post_type_query( $url, $post_type_arg = null ) {
	if ( strpos( $url, 'post_type=' ) !== false ) {
		return preg_replace( '/&%.+/', '', $url );
	}

	$post_type = '';

	// Only change post type if post type arg isn't the same.
	if ( $post_type_arg !== $post_type ) {
		$post_type = $post_type_arg;
	}

	// Add post type if empty.
	if ( empty( $post_type ) ) {
		$post_id = papi_get_post_id();

		if ( $post_id === 0 ) {
			$post_type = papi_get_or_post( 'post_type' );
		} else {
			$post_type = get_post_type( $post_id );
		}

		if ( empty( $post_type ) ) {
			$post_type = $post_type_arg;
		}

		if ( empty( $post_type ) ) {
			$post_type = 'post';
		}
	}

	// Add right query string character.
	if ( ! empty( $post_type ) ) {
		if ( substr( $url, - 1, 1 ) !== '&' ) {
			$url .= '&';
		}

		$url .= 'post_type=' . $post_type;
	}

	return $url;
}

/**
 * Append query strings from existing request.
 *
 * @param  string $first_character
 * @param  array  $allowed_keys
 *
 * @return string
 */
function papi_include_query_strings( $first_character = '?', array $allowed_keys = [] ) {
	if ( empty( $allowed_keys ) ) {
		return '';
	}

	$include = array_intersect_key( $_GET, array_flip( $allowed_keys ) );

	if ( empty( $include ) ) {
		return '';
	}

	return $first_character . http_build_query( $include );
}
