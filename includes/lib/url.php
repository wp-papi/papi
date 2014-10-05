<?php

/**
 * Papi Url functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

	if ( substr( $query, 1, 1 ) === '&' || substr( $query, 1, 1 ) === '?' ) {
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
 * Load post new action
 * Redirect to right url if no page type is set.
 *
 * @since 1.0.0
 */

function _papi_load_post_new() {
	$request_uri = $_SERVER['REQUEST_URI'];

	if ( strpos( $request_uri, 'page_type=' ) === false && strpos( $request_uri, 'papi-bypass=true' ) === false ) {
		$parsed_url = parse_url( $request_uri );
		$post_type  = _papi_get_wp_post_type();

		// Get the core settings.
		$settings = _papi_get_settings();

		// Check if we should show one post type or not and create the right url for that.
		if ( isset( $settings[ $post_type ] ) && isset( $settings[ $post_type ]['only_page_type'] ) ) {
			$url = _papi_get_page_new_url( $settings[ $post_type ]['only_page_type'], false );
		} else {
			$url = "edit.php?page=papi-add-new-page,$post_type&" . $parsed_url['query'];
		}

		wp_safe_redirect( $url );
		exit;
	}
}

add_action( 'load-post-new.php', '_papi_load_post_new' );
