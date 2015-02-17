<?php

/**
 * Papi field functions.
 *
 * @package Papi
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

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

function papi_field( $post_id = null, $name = null, $default = null, $admin = false ) {
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

		$post_id = papi_get_post_id();
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
	$name = papi_remove_papi( $name );

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

	return papi_field_value( $names, $value, $default );
}

/**
 * Get current properties for the page.
 *
 * @since 1.2.0
 *
 * @return array
 */

function papi_fields() {
	$page = current_page();

	if ( empty( $page ) ) {
		return array();
	}

	$page_type = $page->get_page_type();

	if ( empty( $page_type ) ) {
		return array();
	}

	$arr = array();
	$boxes = $page_type->get_boxes();

	if ( empty ( $boxes ) || ! is_array( $boxes ) ) {
		return array();
	}

	foreach ( $boxes as $box ) {
		if ( count( $box ) < 2 || ! isset( $box[0]['title'] ) ) {
			continue;
		}

		if ( ! isset( $arr[$box[0]['title']] ) ) {
			$arr[$box[0]['title']] = array();
		}

		foreach ( $box[1] as $property ) {
			$arr[$box[0]['title']][] = papi_remove_papi( $property->slug );
		}
	}

	return $arr;
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

function papi_field_shortcode( $atts ) {
	// Try to fetch to post id.
	if ( empty( $atts['id'] ) ) {
		global $post;
		if ( isset( $post ) && isset( $post->ID ) ) {
			$atts['id'] = $post->ID;
		}
	}

	$value = null;

	// Fetch value.
	if ( ! empty( $atts['id'] ) ) {
		$value = papi_field( $atts['id'], $atts['name'], $atts['default'] );
	}

	// Set default value if is null.
	if ( empty( $atts['default'] ) ) {
		$atts['default'] = '';
	}

	// Return empty string if null or the value.
	return $value == null ? $atts['default'] : $value;
}

add_shortcode( 'papi_field', 'papi_field_shortcode' );

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

function papi_field_value( $names, $value, $default ) {
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

/**
 * Echo the property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default
 *
 * @since 1.0.0
 */

function the_papi_field( $post_id = null, $name = null, $default = null ) {
	$value = papi_field( $post_id, $name, $default );

	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}

	echo $value;
}
