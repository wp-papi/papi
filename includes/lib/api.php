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
 * Get property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function papi_field( $post_id = null, $name = null, $default = null ) {
	return _papi_field( $post_id, $name, $default );
}

/**
 * Create a new property array or rendering a template property file.
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
 * Create a new tab array or rendering a template tab file.
 *
 * @param string|array $file_or_options
 * @param array $properties
 *
 * @since 1.0.0
 *
 * @return array
 */

function papi_tab ( $file_or_options, $properties = array() ) {
	list( $options, $properties ) = _papi_get_options_and_properties( $file_or_options, $properties, false );

	// The tab key is important, it's says that we should render a tab meta box.
	// This may change in later version of Papi.
	return array(
		'options'    => $options,
		'properties' => $properties,
		'tab'        => true
	);
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

/**
 * Echo the property value for property on a page.
 *
 * @param int $post_id
 * @param string $name
 * @param mixed $default
 *
 * @since 1.0.0
 */

function the_papi_field( $post_id = null, $name = null, $default = null ) {
	$value = papi_field( $post_id, $name, $default );

	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}

	echo $value;
}
