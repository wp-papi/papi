<?php

/**
 * Papi Utilities functions for testing.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Create property post data.
 *
 * @param array $values
 * @param mixed $post
 */

function papi_test_create_property_post_data( $values, $post = null ) {
	$property_type_slug = papi_html_name( papi_get_property_type_key( $values['slug'] ) );

	$data = array();
	$data[$values['slug']] = $values['value'];
	$data[$property_type_slug] = $values['type'];

	if ( ! is_null( $post ) ) {
		return array_merge( $post, $data );
	}

	return $data;
}

/**
 * Get private or protected method.
 *
 * @param mixed $class
 * @param string $method
 *
 * @return object
 */

function papi_test_get_method( $class, $method ) {
	$method = new ReflectionMethod( $class, $method );
	$method->setAccessible( true );
	return $method;
}
