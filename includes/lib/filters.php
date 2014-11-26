<?php

/**
 * Papi filters functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run apply filters with Papi prefix.
 *
 * @param string $tag
 * @param mixed $value
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_apply_filters( $tag, $value ) {
	return apply_filters( 'papi_' . $tag, $value );
}

/**
 * Get all registered page type directories.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_get_get_page_type_directories() {
	$directories = _papi_apply_filters( 'page_type_directories', array() );

	if ( is_string( $directories ) ) {
		return array( $directories );
	}

	if ( ! is_array( $directories ) ) {
		return array();
	}

	return array_filter( $directories, function ($directory) {
		return is_string( $directory );
	});
}

/**
 * Format the value of the property before we output it to the application.
 *
 * @param string $type
 * @param mixed $value
 * @param string $slug
 * @param int $post_id
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_format_value( $type, $value, $slug, $post_id ) {
	return _papi_apply_filters( 'format_value_' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
}

/**
 * This filter is applied after the $value is loaded in the database.
 *
 * @param string $type
 * @param mixed $value
 * @param string $slug
 * @param int $post_id
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_load_value( $type, $value, $slug, $post_id ) {
	return _papi_apply_filters( 'load_value_' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
}

/**
 * This filter is applied before the $value is saved in the database.
 *
 * @param string $type
 * @param mixed $value
 * @param string $slug
 * @param int $post_id
 *
 * @since 1.0.0ytest
 *
 * @return mixed
 */

function _papi_update_value( $type, $value, $slug, $post_id ) {
	return _papi_apply_filters( 'update_value_' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
}
