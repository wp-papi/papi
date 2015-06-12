<?php

/**
 * Papi option functions.
 *
 * @package Papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check if it's a option page url.
 *
 * @return bool
 */

function papi_is_option_page() {
	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url  = parse_url( $request_uri );

	if ( ! isset( $parsed_url['query'] ) || empty ( $parsed_url['query'] ) ) {
		return '';
	}

	$query = $parsed_url['query'];

	return ! preg_match( '/page\-type\=/', $query ) && preg_match( '/page\=/', $query );
}

/**
 * Get property value for property on a option page.
 *
 * @param string $name
 * @param mixed $default
 *
 * @return mixed
 */

function papi_option( $name, $default = null ) {
	return papi_field( 0, $name, $default, 'option' );
}

/**
 * Echo the property value for property on a option page.
 *
 * @param string $name
 * @param mixed $default
 */

function the_papi_option( $name = null, $default = null ) {
	$value = papi_option( $name, $default );

	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}

	echo $value;
}
