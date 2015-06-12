<?php

/**
 * Papi field functions.
 *
 * @package Papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Get property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default
 * @param string $type
 *
 * @return mixed
 */

function papi_field( $post_id = null, $name = null, $default = null, $type = 'post' ) {
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$default = $name;
		$name    = $post_id;
		$post_id = null;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 && $type === 'post' ) {
		return $default;
	}

	// Return the default value if we don't have a name.
	if ( empty( $name ) ) {
		return $default;
	}

	$cache_key = papi_get_cache_key( $name, $post_id );
	$value     = wp_cache_get( $cache_key );

	if ( $value === null || $value === false ) {
		// Check for "dot" notation.
		$names = explode( '.', $name );
		$name  = $names[0];
		$names = array_slice( $names, 1 );

		// Get the right page for right data type.
		$data_page = papi_get_page( $post_id, $type );

		// Return the default value if we don't have a valid page.
		if ( is_null( $data_page ) ) {
			return $default;
		}

		$value = papi_field_value( $names, $data_page->get_value( $name ), $default );

		if ( papi_is_empty( $value ) ) {
			return $default;
		}

		wp_cache_set( $cache_key, $value );
	}

	return $value;
}

/**
 * Get current properties for the page.
 *
 * @param int $post_id
 *
 * @return array
 */

function papi_fields( $post_id = 0 ) {
	$page = papi_get_page( $post_id );

	if ( empty( $page ) ) {
		return [];
	}

	$cache_key = papi_get_cache_key( 'page', $page->id );
	$value     = wp_cache_get( $cache_key );

	if ( $value === false ) {
		$page_type = $page->get_page_type();

		if ( empty( $page_type ) ) {
			return [];
		}

		$value = [];
		$boxes = $page_type->get_boxes();

		foreach ( $boxes as $box ) {
			if ( count( $box ) < 2 || empty( $box[0]['title'] ) || ! is_array( $box[1] ) ) {
				continue;
			}

			if ( ! isset( $value[$box[0]['title']] ) ) {
				$value[$box[0]['title']] = [];
			}

			foreach ( $box[1] as $property ) {
				$value[$box[0]['title']][] = $property->get_slug();
			}
		}

		wp_cache_set( $cache_key, $value );
	}

	return $value;
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
	$atts['id'] = isset( $atts['id'] ) ? $atts['id'] : 0;
	$atts['id'] = papi_get_post_id( $atts['id'] );
	$default    = isset( $atts['default'] ) ? $atts['default'] : '';

	if ( empty( $atts['id'] ) || empty( $atts['name'] ) ) {
		$value = $default;
	} else {
		$value = papi_field( $atts['id'], $atts['name'], $default );
	}

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	return $value;
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
 * @return mixed
 */

function papi_field_value( $names, $value, $default = null ) {
	if ( empty( $value ) && is_null( $value ) ) {
		return $default;
	}

	if ( ! empty( $names ) && ( is_object( $value ) || is_array( $value ) ) ) {

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
 */

function the_papi_field( $post_id = null, $name = null, $default = null ) {
	$value = papi_field( $post_id, $name, $default );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	echo $value;
}
