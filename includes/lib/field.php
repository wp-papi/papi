<?php

/**
 * Papi field functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default
 * @param bool $admin Only used in WordPress admin
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_field( $post_id = null, $name = null, $default = null, $admin = false ) {
	// Check if we have a post id or not.
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$default = $name;
		$name    = $post_id;
		$post_id = null;
	}

	// If it's a numeric value, let's convert it to int.
	if ( is_numeric( $post_id ) ) {
		$post_id = intval( $post_id );
	} else {

		$post_id = _papi_get_post_id();
	}

	// Return the default value if we don't have a name.
	if ( is_null( $name ) ) {
		return $default;
	}

	// Check for "dot" notation.
	$names = explode( '.', $name );

	// Get the first value in the array.
	$name = $names[0];

	// Remove any `papi_` stuff if it exists.
	$name = _papi_remove_papi( $name );

	// Remove the first value of the array.
	$names = array_slice( $names, 1 );

	// Get the page.
	$page = papi_get_page( $post_id );

	// Return the default value if we don't have a WordPress post on the page object.
	if ( is_null( $page ) || ! $page->has_post() ) {
		return $default;
	}

	if ( $admin ) {
		$value = $page->get_value( $name, true );
	} else {
		$value = $page->$name;
	}

	return _papi_field_value( $names, $value, $default );
}

/**
 * Shortcode for `papi_field` function.
 *
 * [papi_field id=1 name="field_name" default="Default value"][/papi_field]
 *
 * @param array $atts
 *
 * @return mixed
 */

function _papi_field_shortcode( $atts ) {
	// Try to fetch to post id.
	if ( empty( $atts['id'] ) ) {
		global $post;
		if ( isset( $post ) && isset( $post->ID ) ) {
			$atts['id'] = $post->ID;
		}
	}

	// Fetch value.
	if ( ! empty( $atts['id'] ) ) {
		$value = papi_field( $atts['id'], $atts['name'], $atts['default'] );
	}

	// Set default value if is null.
	if ( empty( $atts['default'] ) ) {
		$atts['default'] = '';
	}

	// Return empty string if null or the value.
	return ! isset( $value ) || $value == null ? $atts['default'] : $value;
}

add_shortcode( 'papi_field', '_papi_field_shortcode' );

/**
 * Get field value by keys.
 *
 * Example:
 *
 * "image.url" will get the url value in the array.
 *
 * @param array $names
 * @param mixed $value
 * @param mixed $default
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_field_value( $names, $value, $default ) {
	// Return default value we don't have a value.
	if ( empty( $value ) && is_null( $value ) ) {
		return $default;
	}

	// Check if it's a array value or object.
	if ( ! empty( $names ) && ( is_object( $value ) || is_array( $value ) ) ) {

		// Convert object to array.
		if ( is_object( $value ) ) {
			$value = (array) $value;
		}

		foreach ( $names as $key ) {
			if ( isset( $value[ $key ] ) ) {
				$value = $value[ $key ];
			}
		}
	}

	return $value;
}
