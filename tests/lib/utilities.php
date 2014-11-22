<?php

/**
 * Papi Utilities functions for testing.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function _papi_test_create_property_post_data ($values, $post) {
	$property_type_slug = _papi_html_name( _papi_get_property_type_key( $values['slug'] ) );

	$data = array();
	$data[$values['slug']] = $values['value'];
	$data[$property_type_slug] = $values['type'];

	if ( isset( $post ) ) {
		return array_merge( $post, $data );
	}

	return $data;
}
