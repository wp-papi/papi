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
 * Get Papi settings.
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_settings() {
	return apply_filters( 'papi/settings', array() );
}

/**
 * This filter is applied after the $value is loaded in the database.
 *
 * @param mixed $value
 * @param int $post_id
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_load_value( $type, $value, $post_id ) {
	return apply_filters( 'papi/load_value/' . _papi_get_property_short_type( $type ), $value, $post_id );
}

/**
 * Format the value of the property before we output it to the application.
 *
 * @param mixed $value
 * @param int $post_id
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_format_value( $type, $value, $post_id ) {
	return apply_filters( 'papi/format_value/' . _papi_get_property_short_type( $type ), $value, $post_id );
}

/**
 * This filter is applied before the $value is saved in the database.
 *
 * @param mixed $value
 * @param int $post_id
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function _papi_update_value( $type, $value, $post_id ) {
	return apply_filters( 'papi/update_value/' . _papi_get_property_short_type( $type ), $value, $post_id );
}
