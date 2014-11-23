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
 * Get the current page. Like in EPiServer.
 *
 * @since 1.0.0
 *
 * @return Papi_Page|null
 */

function current_page() {
	return papi_get_page();
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
 * Get the page.
 *
 * @param int $post_id The post id.
 *
 * @since 1.0.0
 *
 * @return Papi_Page|null
 */

function papi_get_page( $post_id = null ) {
	$post_id = _papi_get_post_id( $post_id );
	$page    = new Papi_Page( $post_id );

	if ( ! $page->has_post() ) {
		return null;
	}

	return $page;
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
		return _papi_get_property_options( $file_or_options );
	}

	if ( is_string( $file_or_options ) && is_array( $values ) ) {
		return _papi_template( $file_or_options, $values, true );
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

function papi_tab( $file_or_options, $properties = array() ) {
	list( $options, $properties ) = _papi_get_options_and_properties( $file_or_options, $properties, false);

	// The tab key is important, it's says that we should render a tab meta box.
	// This may change in later version of Papi.
	return (object)array(
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
	return _papi_template( $file, $values );
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
