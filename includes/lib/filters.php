<?php

/**
 * Papi filters functions.
 *
 * @package Papi
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	return apply_filters( 'papi/format_value/' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
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
	return apply_filters( 'papi/load_value/' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
}

/**
 * Get the only page type that will be used for the given post type.
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_filter_settings_only_page_type( $post_type ) {
	$page_type = apply_filters( 'papi/settings/only_page_type_' . $post_type, '' );

	if ( ! is_string( $page_type ) ) {
		return '';
	}

	return str_replace( '.php', '', $page_type );
}

/**
 * Get all registered page type directories.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_filter_settings_directories() {
	$directories = apply_filters( 'papi/settings/directories', array() );

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
 * Get standard page name for the given post type.
 *
 * @param string $post_type
 *
 * @since 1.2.0
 *
 * @return string
 */

function _papi_filter_standard_page_name( $post_type ) {
	return apply_filters( 'papi/settings/standard_page_name_' . $post_type, __( 'Standard Page', 'papi' ) );
}

/**
 * Show standard page type on the given post type.
 *
 * @param string $post_type
 *
 * @since 1.0.0
 *
 * @return bool
 */

function _papi_filter_settings_standard_page_type( $post_type ) {
	return apply_filters( 'papi/settings/standard_page_type_' . $post_type, true ) === true;
}

/**
 * Get the default sort order that is 1000.
 *
 * @since 1.0.0
 *
 * @return int
 */

function _papi_filter_settings_sort_order() {
	return intval( apply_filters( 'papi/settings/sort_order', 1000 ) );
}

/**
 * This filter is applied before the $value is saved in the database.
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

function _papi_filter_update_value( $type, $value, $slug, $post_id ) {
	return apply_filters( 'papi/update_value/' . _papi_get_property_short_type( $type ), $value, $slug, $post_id );
}
