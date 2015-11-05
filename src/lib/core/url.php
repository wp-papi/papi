<?php

/**
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param  string $page_type
 * @param  bool   $append_admin_url
 * @param  string $post_type
 * @param  array  $exclude
 *
 * @return string
 */
function papi_get_page_new_url( $page_type, $append_admin_url = true, $post_type = null, $exclude = [] ) {
	$admin_url = $append_admin_url ? get_admin_url() : '';

	$admin_url = $admin_url . 'post-new.php?page_type=' . $page_type . papi_get_page_query_strings( '&', $exclude );

	if ( ! is_null( $post_type ) && in_array( 'post_type', $exclude ) ) {
		$admin_url = str_replace( '&&', '&', $admin_url . '&post_type=' . $post_type );
	}

	return papi_append_post_type_query( $admin_url, $post_type );
}

/**
 * Get page query strings.
 *
 * @param  string $first_char
 * @param  array  $exclude
 *
 * @return string
 */
function papi_get_page_query_strings( $first_char = '&', $exclude = [] ) {
	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url  = parse_url( $request_uri );

	if ( ! isset( $parsed_url['query'] ) || empty( $parsed_url['query'] ) ) {
		return '';
	}

	$query = $parsed_url['query'];
	$query = preg_replace( '/page\=[a-z-,]+/', '', $query );
	$query = str_replace( '?', '', $query );
	$query = explode( '&', $query );

	$query = array_filter( $query, function ( $q ) use ( $exclude ) {
		$q = explode( '=', $q );

		if ( empty( $q ) || empty( $q[0] ) ) {
			return false;
		}

		if ( empty( $exclude ) ) {
			return true;
		}

		return ! in_array( $q[0], $exclude );
	} );

	$query = implode( '&', $query );
	$query = $first_char . $query;

	if ( in_array( 'post_type', $exclude ) ) {
		return $query;
	}

	if ( $query === $first_char ) {
		$query = '';
	}

	$empty_query = empty( $query );
	$query = papi_append_post_type_query( $query );

	if ( $empty_query ) {
		return $first_char . substr( $query, 1 );
	}

	return $query;
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

	$post_id = papi_get_post_id();

	if ( $post_id === 0 ) {
		$post_type = papi_get_or_post( 'post_type' );
	} else {
		$post_type = get_post_type( $post_id );
	}

	if ( ! empty( $post_type_arg ) && empty( $post_type ) ) {
		$post_type = $post_type_arg;
	}

	if ( empty( $post_type ) ) {
		$post_type = 'post';
	}

	if ( ! empty( $post_type ) ) {
		if ( substr( $url, - 1, 1 ) !== '&' ) {
			$url .= '&';
		}

		$url .= 'post_type=' . $post_type;
	}

	return $url;
}
