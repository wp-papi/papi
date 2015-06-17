<?php

/**
 * Papi field functions.
 *
 * @package Papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Delete value in the database.
 *
 * @param int $post_id
 * @param string $slug
 * @param string $type
 *
 * @return bool
 */

function papi_delete_field( $post_id = null, $slug = null, $type = 'post' ) {
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$slug    = $post_id;
		$post_id = null;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 && $type === 'post' ) {
		return false;
	}

	$page = papi_get_page( $post_id, $type );

	if ( is_null( $page ) ) {
		return false;
	}

	$property = $page->get_property( $slug );

	if ( ! papi_is_property( $property ) ) {
		return false;
	}

	$cache_key = papi_get_cache_key( $slug, $post_id );
	wp_cache_set( $cache_key, false );

	return $property->delete_value( $slug, $post_id );
}

/**
 * Get value for a property on a page.
 *
 * @param int $post_id
 * @param string $slug
 * @param mixed $default
 * @param string $type
 *
 * @return mixed
 */

function papi_field( $post_id = null, $slug = null, $default = null, $type = 'post' ) {
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$default = $slug;
		$slug    = $post_id;
		$post_id = null;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 && $type === 'post' ) {
		return $default;
	}

	// Return the default value if we don't have a name.
	if ( empty( $slug ) ) {
		return $default;
	}

	$cache_key = papi_get_cache_key( $slug, $post_id );
	$value     = wp_cache_get( $cache_key );

	if ( $value === null || $value === false ) {
		// Check for "dot" notation.
		$slugs = explode( '.', $slug );
		$slug  = $slugs[0];
		$slugs = array_slice( $slugs, 1 );

		// Get the right page for right data type.
		$page = papi_get_page( $post_id, $type );

		// Return the default value if we don't have a valid page.
		if ( is_null( $page ) ) {
			return $default;
		}

		$value = papi_field_value( $slugs, $page->get_value( $slug ), $default );

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
 * [papi_field id=1 slug="field_name" default="Default value"][/papi_field]
 *
 * @param array $atts
 *
 * @return mixed
 */

function papi_field_shortcode( $atts ) {
	$atts['id'] = isset( $atts['id'] ) ? $atts['id'] : 0;
	$atts['id'] = papi_get_post_id( $atts['id'] );
	$default    = isset( $atts['default'] ) ? $atts['default'] : '';

	if ( empty( $atts['id'] ) || empty( $atts['slug'] ) ) {
		$value = $default;
	} else {
		$value = papi_field( $atts['id'], $atts['slug'], $default );
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
 * @param array $slugs
 * @param mixed $value
 * @param mixed $default
 *
 * @return mixed
 */

function papi_field_value( $slugs, $value, $default = null ) {
	if ( empty( $value ) && is_null( $value ) ) {
		return $default;
	}

	if ( ! empty( $slugs ) && ( is_object( $value ) || is_array( $value ) ) ) {

		if ( is_object( $value ) ) {
			$value = (array) $value;
		}

		foreach ( $slugs as $key ) {
			if ( isset( $value[ $key ] ) ) {
				$value = $value[ $key ];
			}
		}
	}

	return $value;
}

/**
 * Update field with new value. The old value will be deleted.
 *
 * @param int $post_id
 * @param string $slug
 * @param string $type
 *
 * @return bool
 */

function papi_update_field( $post_id = null, $slug = null, $value = null, $type = 'post' ) {
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$slug    = $post_id;
		$post_id = null;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 && $type === 'post' ) {
		return false;
	}

	$page = papi_get_page( $post_id, $type );

	if ( is_null( $page ) ) {
		return false;
	}

	$property = $page->get_property( $slug );

	if ( ! papi_is_property( $property ) ) {
		return false;
	}

	papi_delete_field( $post_id, $slug, $type );

	return papi_property_update_meta( [
		'post_id'       => $post_id,
		'slug'          => $slug,
		'value'         => $property->update_value( $value, $slug, $post_id )
	] );
}

/**
 * Echo the value for property on a page.
 *
 * @param int $post_id
 * @param string $slug
 * @param mixed $default
 */

function the_papi_field( $post_id = null, $slug = null, $default = null ) {
	$value = papi_field( $post_id, $slug, $default );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	echo $value;
}
