<?php

/**
 * Delete value in the database.
 *
 * @param  int    $id
 * @param  string $slug
 * @param  string $type
 *
 * @return bool
 */
function papi_delete_field( $id = null, $slug = null, $type = 'post' ) {
	if ( ! is_numeric( $id ) && is_string( $id ) ) {
		$type = $slug;
		$slug = $id;
		$id   = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return false;
	}

	$id = papi_get_meta_id( $type, $id );

	if ( $id === 0 && papi_get_meta_type( $type ) !== 'option' ) {
		return false;
	}

	$store = papi_get_meta_store( $id, $type );

	if ( is_null( $store ) ) {
		return false;
	}

	$property = $store->get_property( $slug );

	if ( ! papi_is_property( $property ) ) {
		return false;
	}

	papi_cache_delete( $slug, $id, $type );

	papi_action_delete_value( $type, $slug, $id );

	return $property->delete_value( $slug, $id, $type );
}

/**
 * Shortcode for `papi_get_field` function.
 *
 * [papi_field id=1 slug="property_slug" default="Default value"][/papi_field]
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
			if ( isset( $value[$key] ) ) {
				$value = $value[$key];
			}
		}
	}

	return $value;
}

/**
 * Get value for a property on a page.
 *
 * @param  int    $id
 * @param  string $slug
 * @param  mixed  $default
 * @param  string $type
 *
 * @return mixed
 */
function papi_get_field( $id = null, $slug = null, $default = null, $type = 'post' ) {
	if ( ! is_numeric( $id ) && is_string( $id ) ) {
		$type    = $default;
		$default = $slug;
		$slug    = $id;
		$id      = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return $default;
	}

	$id = papi_get_meta_id( $type, $id );

	if ( $id === 0 && papi_get_meta_type( $type ) !== 'option' ) {
		return $default;
	}

	$value = papi_cache_get( $slug, $id, $type );

	if ( $value === null || $value === false ) {
		// Check for "dot" notation.
		$slugs = explode( '.', $slug );
		$slug  = $slugs[0];
		$slugs = array_slice( $slugs, 1 );

		// Get the right store for right entry type.
		$store = papi_get_meta_store( $id, $type );

		// Return the default value if we don't have a valid store.
		if ( is_null( $store ) ) {
			return $default;
		}

		$value = papi_field_value( $slugs, $store->get_value( $slug ), $default );

		if ( papi_is_empty( $value ) ) {
			return $default;
		}

		papi_cache_set( $slug, $id, $value, $type );
	}

	return $value;
}

/**
 * Get boxes with properties slug for a page.
 *
 * Since 3.0.0 the param `$only_slugs` exists, if true
 * will it only return the slugs without boxes title.
 *
 * @param  int    $post_id
 * @param  string $only_slugs
 * @param  string $type
 *
 * @return array
 */
function papi_get_slugs( $post_id = 0, $only_slugs = false, $type = 'post' ) {
	$store = papi_get_meta_store( $post_id, $type );

	if ( $store instanceof Papi_Core_Meta_Store === false ) {
		return [];
	}

	$entry_type = $store->get_type_class();

	if ( empty( $entry_type ) ) {
		return [];
	}

	$value = [];
	$boxes = $entry_type->get_boxes();

	foreach ( $boxes as $box ) {
		if ( ! $only_slugs ) {
			$title = $box->title;

			if ( ! isset( $value[$title] ) ) {
				$value[$title] = [];
			}
		}

		foreach ( $box->properties as $property ) {
			$slug = $property->get_slug( true );

			if ( $only_slugs ) {
				$value[] = $slug;
			} else {
				$value[$title][] = $slug;
			}
		}
	}

	return $only_slugs ? array_unique( $value ) : $value;
}

/**
 * Update field with new value. The old value will be deleted.
 *
 * @param  int    $id
 * @param  string $slug
 * @param  mixed  $value
 * @param  string $type
 *
 * @return bool
 */
function papi_update_field( $id = null, $slug = null, $value = null, $type = 'post' ) {
	if ( ! is_numeric( $id ) && is_string( $id ) ) {
		$type  = $value;
		$value = $slug;
		$slug  = $id;
		$id    = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return false;
	}

	if ( papi_is_empty( $value ) ) {
		return papi_delete_field( $id, $slug, $type );
	}

	$id = papi_get_meta_id( $type, $id );

	if ( $id === 0 && papi_get_meta_type( $type ) !== 'option' ) {
		return false;
	}

	$store = papi_get_meta_store( $id, $type );

	if ( is_null( $store ) ) {
		return false;
	}

	$property = $store->get_property( $slug );

	if ( ! papi_is_property( $property ) ) {
		return false;
	}

	papi_delete_field( $id, $slug, $type );

	$value = $property->update_value( $value, $slug, $id );
	$value = papi_filter_update_value( $property->get_option( 'type' ), $value, $slug, $id );

	return papi_update_property_meta_value( [
		'type'  => $type,
		'id'    => $id,
		'slug'  => $slug,
		'value' => $value
	] );
}

/**
 * Echo the value for property.
 *
 * @param int    $id
 * @param string $slug
 * @param mixed  $default
 */
function the_papi_field( $id = null, $slug = null, $default = null ) {
	$value = papi_get_field( $id, $slug, $default );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	if ( is_object( $value ) ) {
		// @codeCoverageIgnoreStart
		$value = print_r( $value, true );
		// @codeCoverageIgnoreEnd
	}

	echo $value;
}
