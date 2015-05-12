<?php

/**
 * Papi Utilities functions for testing.
 *
 * @package Papi
 * @version 1.0.0
 */

/**
 * Create property post data.
 *
 * @param array $values
 * @param mixed $post
 *
 * @return array
 */

function papi_test_create_property_post_data( $values, $post = null ) {
	$property_type_slug = papi_html_name( papi_get_property_type_key( $values['slug'] ) );

	$data = array();
	$data[$values['slug']] = $values['value'];

	$property_type_options = papi_get_property_options( (array) $values['type'] );

	$property_type_options = base64_encode( serialize( $property_type_options ) );

	$data[$property_type_slug] = $property_type_options;

	if ( ! is_null( $post ) ) {
		return array_merge( $post, $data );
	}

	return $data;
}

/**
 * Get real to test fixtures directory with given path.
 *
 * @param string
 *
 * @return string
 */

function papi_test_get_fixtures_path( $path ) {
	if ( ! empty( $path ) && $path[0] !== '/' ) {
		$path = '/' . $path;
	}

	return __DIR__ . '/fixtures' . $path;
}
