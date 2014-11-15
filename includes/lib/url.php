<?php

/**
 * Papi url functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the url to 'post-new.php' with query string of the page type to load.
 *
 * @param string $page_type
 * @param bool $append_admin_url Default true
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_page_new_url( $page_type, $append_admin_url = true ) {
	$admin_url = $append_admin_url ? get_admin_url() : '';

	return $admin_url . 'post-new.php?page_type=' . $page_type . _papi_get_page_query_strings();
}

/**
 * Get page query strings.
 *
 * @param string $first_char
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_page_query_strings( $first_char = '&' ) {
	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url  = parse_url( $request_uri );
	$query       = $parsed_url['query'];
	$query       = preg_replace( '/page\=[a-z-,]+/', '', $query );
	$query       = str_replace( '?', '', $query );

	if ( substr( $query, 0, 1 ) === '&' || substr( $query, 0, 1 ) === '?' ) {
		$query[0] = $first_char;
	} else {
		$query = $first_char . $query;
	}

	// Remove last char if it's a & or ?
	if ( substr( $query, - 1, 1 ) === '&' || substr( $query, - 1, 1 ) === '?' ) {
		$query = substr( $query, 0, - 1 );
	}

	return $query;
}
