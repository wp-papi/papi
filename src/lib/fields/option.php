<?php

/**
 * Delete value in the database.
 *
 * @param  string $slug
 *
 * @return bool
 */
function papi_delete_option( $slug ) {
	return papi_delete_field( 0, $slug, 'option' );
}

/**
 * Get property value for property on a option page.
 *
 * @param  string $slug
 * @param  mixed  $default
 *
 * @return mixed
 */
function papi_get_option( $slug, $default = null ) {
	return papi_get_field( 0, $slug, $default, 'option' );
}

/**
 * Shortcode for `papi_get_option` function.
 *
 * [papi_option slug="property_slug" default="Default value"][/papi_option]
 *
 * @param  array $atts
 *
 * @return mixed
 */
function papi_option_shortcode( $atts ) {
	$default = isset( $atts['default'] ) ? $atts['default'] : '';

	if ( empty( $atts['slug'] ) ) {
		$value = $default;
	} else {
		$value = papi_get_option( $atts['slug'], $default );
	}

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	return $value;
}

add_shortcode( 'papi_option', 'papi_option_shortcode' );

/**
 * Update field with new value. The old value will be deleted.
 *
 * @param  string $slug
 * @param  mixed  $value
 *
 * @return bool
 */
function papi_update_option( $slug, $value = null ) {
	return papi_update_field( 0, $slug, $value, 'option' );
}

/**
 * Echo the value for property on a option page.
 *
 * @param string $slug
 * @param mixed  $default
 */
function the_papi_option( $slug = null, $default = null ) {
	$value = papi_get_option( $slug, $default );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	if ( is_object( $value ) ) {
		$value = papi_convert_to_string( $value );
	}

	echo $value; // phpcodesniffer xss whitelist
}
