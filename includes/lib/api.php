<?php

/**
 * Papi API functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add new property to the page using array or rendering property template file.
 *
 * @param string|array $file_or_options
 * @param array $values
 *
 * @since 1.0.0
 *
 * @return array
 */

function papi_property( $file_or_options, $values = array() ) {
	if ( is_array( $file_or_options ) ) {
		return $file_or_options;
	}

	if ( is_string( $file_or_options ) && is_array( $values ) ) {
		return papi_template( $file_or_options, $values );
	}

	return array();
}

/**
 * Load a template array file and merge values with it.
 *
 * @param string $file
 * @param array $values
 *
 * @since 1.0.0
 *
 * @return array
 */

function papi_template( $file, $values = array() ) {
	$filepath = _papi_get_file_path( $file );

	if ( ! is_file( $filepath ) || empty( $filepath ) ) {
		return array();
	}

	$template = require($filepath);

	return array_merge( $template, $values );
}
