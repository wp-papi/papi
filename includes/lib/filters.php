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
 * Get the default sort order that is 1000.
 *
 * @since 1.0.0
 *
 * @return int
 */

function _papi_filter_default_sort_order() {
	return intval( apply_filters( 'papi_default_sort_order', 1000 ) );
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

function _papi_filter_format_value( $type, $value, $slug, $post_id ) {
	return apply_filters( 'papi_format_value_' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
}

/**
 * Get the only page type that will be used for the given post type.
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_filter_only_page_type ( $post_type ) {
	$page_type = apply_filters( 'papi_only_page_type_' . $post_type, '' );

	if ( ! is_string( $page_type ) ) {
		return '';
	}

	return $page_type;
}

/**
 * Show standard page type on the given post type.
 *
 * @since 1.0.0
 *
 * @return bool
 */

function _papi_filter_show_standard_page_type_on( $post_type ) {
	return apply_filters( 'papi_show_standard_page_type_on_' . $post_type, true ) === true;
}

/**
 * Get all registered page type directories.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_filter_page_type_directories() {
	$directories = apply_filters( 'papi_page_type_directories', array() );

	if ( is_string( $directories ) ) {
		return array( $directories );
	}

	if ( ! is_array( $directories ) ) {
		return array();
	}

	return array_filter( $directories, function ( $directory ) {
		return is_string( $directory );
	} );
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

function _papi_filter_load_value( $type, $value, $slug, $post_id ) {
	return apply_filters( 'papi_load_value_' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
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

function _papi_filter_update_value( $type, $value, $slug, $post_id ) {
	return apply_filters( 'papi_update_value_' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
}
