<?php

/**
 * Papi filters functions.
 *
 * @package Papi
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Format the value of the property before it's returned to the theme.
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

function papi_filter_format_value( $type, $value, $slug, $post_id ) {
	return apply_filters( 'papi/format_value/' . papi_get_property_short_type( $type ), $value, $slug, $post_id );
}

/**
 * This filter is applied after the value is loaded in the database.
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

function papi_filter_load_value( $type, $value, $slug, $post_id ) {
	return apply_filters( 'papi/load_value/' . papi_get_property_short_type( $type ), $value, $slug, $post_id );
}

/**
 * Get the only page type that will be used for the given post type.
 *
 * @param string $post_type
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_filter_settings_only_page_type( $post_type ) {
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

function papi_filter_settings_directories() {
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
 * Load page type from post query string.
 *
 * @since 1.2.0
 *
 * @return string
 */

function papi_filter_settings_page_type_from_post_qs() {
	return apply_filters( 'papi/settings/page_type_from_post_qs', 'from_post' );
}

/**
 * Show page type in add new page view for the given post type.
 *
 * @param string $post_type
 * @param string|object $page_type
 *
 * @since 1.2.0
 *
 * @return bool
 */

function papi_filter_show_page_type( $post_type, $page_type ) {
	if ( is_object( $page_type ) && method_exists( $page_type, 'get_id' ) ) {
		$page_type = $page_type->get_id();
	}

	return apply_filters( 'papi/settings/show_page_type_' . $post_type, $page_type ) !== false;
}

/**
 * Get standard page description for the given post type.
 *
 * @param string $post_type
 *
 * @since 1.2.0
 *
 * @return string
 */

function papi_filter_standard_page_description( $post_type ) {
	return apply_filters( 'papi/settings/standard_page_description_' . $post_type, __( 'Just the normal WordPress page', 'papi' ) );
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

function papi_filter_standard_page_name( $post_type ) {
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

function papi_filter_settings_standard_page_type( $post_type ) {
	return apply_filters( 'papi/settings/standard_page_type_' . $post_type, true ) === true;
}

/**
 * Get standard page thumbnail for the given post type.
 *
 * @param string $post_type
 *
 * @since 1.2.0
 *
 * @return string
 */

function papi_filter_standard_page_thumbnail( $post_type ) {
	return apply_filters( 'papi/settings/standard_page_thumbnail_' . $post_type, '' );
}

/**
 * Get the default sort order that is 1000.
 *
 * @since 1.0.0
 *
 * @return int
 */

function papi_filter_settings_sort_order() {
	return intval( apply_filters( 'papi/settings/sort_order', 1000 ) );
}

/**
 * This filter is applied before the value is saved in the database.
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

function papi_filter_update_value( $type, $value, $slug, $post_id ) {
	return apply_filters( 'papi/update_value/' . papi_get_property_short_type( $type ), $value, $slug, $post_id );
}
