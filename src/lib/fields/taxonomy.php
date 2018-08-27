<?php

/**
 * Delete property value in the database.
 *
 * @param  int    $term_id
 * @param  string $slug
 *
 * @return bool
 */
function papi_delete_term_field( $term_id, $slug = '' ) {
	if ( ! is_numeric( $term_id ) && is_string( $term_id ) ) {
		$slug    = $term_id;
		$term_id = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return false;
	}

	return papi_delete_field( papi_get_term_id( $term_id ), $slug, 'term' );
}

/**
 * Get property value from the database.
 *
 * @param  int    $term_id
 * @param  string $slug
 * @param  mixed  $default
 *
 * @return mixed
 */
function papi_get_term_field( $term_id = null, $slug = null, $default = null ) {
	if ( ! is_numeric( $term_id ) && is_string( $term_id ) ) {
		$default = $slug;
		$slug    = $term_id;
		$term_id = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return $default;
	}

	return papi_get_field( papi_get_term_id( $term_id ), $slug, $default, 'term' );
}

/**
 * Get boxes with properties slug for a taxonomy.
 *
 * @param  int $id
 * @param  bool $only_slugs
 *
 * @since 3.1.0 `$id` param is optional.
 *
 * @return array
 */
function papi_get_term_slugs( $id = 0, $only_slugs = false ) {
	if ( is_bool( $id ) ) {
		$only_slugs = $id;
		$id         = null;
	}

	return papi_get_slugs( papi_get_term_id( $id ), $only_slugs, 'term' );
}

/**
 * Shortcode for `papi_get_term_field` function.
 *
 * [papi_taxonomy id=1 slug="property_slug" default="Default value"][/papi_taxonomy]
 *
 * @param  array $atts
 *
 * @return mixed
 */
function papi_taxonomy_shortcode( $atts ) {
	$atts['id'] = isset( $atts['id'] ) ? $atts['id'] : 0;
	$atts['id'] = papi_get_term_id( $atts['id'] );
	$default    = isset( $atts['default'] ) ? $atts['default'] : '';

	if ( empty( $atts['id'] ) || empty( $atts['slug'] ) ) {
		$value = $default;
	} else {
		$value = papi_get_term_field( $atts['id'], $atts['slug'], $default );
	}

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	return $value;
}

add_shortcode( 'papi_taxonomy', 'papi_taxonomy_shortcode' );

/**
 * Update property with new value. The old value will be deleted.
 *
 * @param  int    $term_id
 * @param  string $slug
 * @param  mixed  $value
 *
 * @return bool
 */
function papi_update_term_field( $term_id = null, $slug = null, $value = null ) {
	if ( ! is_numeric( $term_id ) && is_string( $term_id ) ) {
		$value   = $slug;
		$slug    = $term_id;
		$term_id = null;
	}

	if ( ! is_string( $slug ) || empty( $slug ) ) {
		return false;
	}

	if ( papi_is_empty( $value ) ) {
		return papi_delete_term_field( $term_id, $slug );
	}

	return papi_update_field( papi_get_term_id( $term_id ), $slug, $value, 'term' );
}

/**
 * Echo the value for property.
 *
 * @param int    $term_id
 * @param string $slug
 * @param mixed  $default
 */
function the_papi_term_field( $term_id = null, $slug = null, $default = null ) {
	$value = papi_get_term_field( $term_id, $slug, $default );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	if ( is_object( $value ) ) {
		$value = papi_convert_to_string( $value );
	}

	echo $value; // phpcodesniffer xss whitelist
}
