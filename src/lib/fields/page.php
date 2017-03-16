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
		$type = empty( $slug ) ? $type : $slug;
		$slug = $id;
		$id   = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return false;
	}

	$id    = papi_get_meta_id( $type, $id );
	$store = papi_get_meta_store( $id, $type );

	if ( is_null( $store ) ) {
		return false;
	}

	$property = $store->get_property( $slug );

	if ( ! papi_is_property( $property ) ) {
		return false;
	}

	papi_cache_delete( $slug, $id, $type );

	/**
	 * Fire action before value is deleted.
	 *
	 * @param string $type
	 * @param string $slug
	 * @param int    $post_id
	 */
	do_action( 'papi/delete_value/' . $type, $slug, $id );

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
		$type    = empty( $default ) ? $type : $default;
		$default = $slug;
		$slug    = $id;
		$id      = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return $default;
	}

	// Check for "dot" notation.
	$slugs = explode( '.', $slug );
	$slug  = $slugs[0];
	$slugs = array_slice( $slugs, 1 );

	// Get right id for right meta type.
	$id = papi_get_meta_id( $type, $id );

	// Get the right store for right entry type.
	$store = papi_get_meta_store( $id, $type );

	// Return the default value if we don't have a valid store.
	if ( is_null( $store ) ) {
		return $default;
	}

	// Get value from store.
	$value = $store->get_value( $id, $slug, $default, $type );

	// Get value by dot keys if any.
	return papi_field_value( $slugs, $value, $default );
}

/**
 * Get boxes with properties slug for a page.
 *
 * @param  int    $id
 * @param  bool   $only_slugs
 * @param  string $type
 *
 * @since 3.0.0 the param `$only_slugs` exists, if true
 * will it only return the slugs without boxes title.
 *
 * @since 3.1.0 `$id` param is optional.
 *
 * @return array
 */
function papi_get_slugs( $id = 0, $only_slugs = false, $type = 'post' ) {
	if ( is_bool( $id ) ) {
		$type       = empty( $only_slugs ) ? $type : $only_slugs;
		$only_slugs = $id;
		$id         = null;
	}

	$store = papi_get_meta_store( $id, $type );

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
		$type  = empty( $value ) ? $value : $type;
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

	$id    = papi_get_meta_id( $type, $id );
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
	$value = papi_filter_update_value( $property->get_option( 'type' ), $value, $slug, $id, $type );

	return papi_data_update( $id, $slug, $value, $type );
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
		$value = papi_convert_to_string( $value );
	}

	echo $value; // phpcodesniffer xss whitelist
}
