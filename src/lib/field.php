<?php

/**
 * Delete value in the database.
 *
 * @param  int    $post_id
 * @param  string $slug
 * @param  string $type
 *
 * @return bool
 */
function papi_delete_field( $post_id = null, $slug = null, $type = 'post' ) {
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$slug    = $post_id;
		$post_id = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return false;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 && $type === Papi_Core_Page::TYPE_POST ) {
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

	papi_cache_delete( $slug, $post_id );

	papi_action_delete_value( $type, $slug, $post_id );

	return $property->delete_value( $slug, $post_id, $type );
}

/**
 * Shortcode for `papi_get_field` function.
 *
 * [papi_field id=1 slug="field_name" default="Default value"][/papi_field]
 *
 * @param  array $atts
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
		$value = papi_get_field( $atts['id'], $atts['slug'], $default );
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
 * @param  array $slugs
 * @param  mixed $value
 * @param  mixed $default
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
 * Get value for a property on a page.
 *
 * @param  int    $post_id
 * @param  string $slug
 * @param  mixed  $default
 * @param  string $type
 *
 * @return mixed
 */
function papi_get_field( $post_id = null, $slug = null, $default = null, $type = 'post' ) {
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$default = $slug;
		$slug    = $post_id;
		$post_id = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return $default;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 && $type === Papi_Core_Page::TYPE_POST ) {
		return $default;
	}

	$value = papi_cache_get( $slug, $post_id );

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

		papi_cache_set( $slug, $post_id, $value );
	}

	return $value;
}

/**
 * Update field with new value. The old value will be deleted.
 *
 * @param  int    $post_id
 * @param  string $slug
 * @param  mixed  $value
 * @param  string $type
 *
 * @return bool
 */
function papi_update_field( $post_id = null, $slug = null, $value = null, $type = 'post' ) {
	if ( ! is_numeric( $post_id ) && is_string( $post_id ) ) {
		$value   = $slug;
		$slug    = $post_id;
		$post_id = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return false;
	}

	if ( papi_is_empty( $value ) ) {
		return papi_delete_field( $post_id, $slug, $type );
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 && $type === Papi_Core_Page::TYPE_POST ) {
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

	$value = $property->update_value( $value, $slug, $post_id );
	$value = papi_filter_update_value( $property->get_option( 'type' ), $value, $slug, $post_id );

	return papi_update_property_meta_value( [
		'post_id'       => $post_id,
		'slug'          => $slug,
		'value'         => $value
	] );
}

/**
 * Echo the value for property on a page.
 *
 * @param int    $post_id
 * @param string $slug
 * @param mixed  $default
 */
function the_papi_field( $post_id = null, $slug = null, $default = null ) {
	$value = papi_get_field( $post_id, $slug, $default );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	echo $value;
}
