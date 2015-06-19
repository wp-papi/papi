<?php

/**
 * Papi deprecated functions.
 *
 * Where functions come to die.
 *
 * @package Papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Get the current page. Like in EPiServer.
 *
 * @deprecated deprecated since version 2.0.0
 *
 * @return Papi_Page|null
 */

function current_page() {
	_deprecated_function( __FUNCTION__, '2.0.0' );
	return papi_get_page();
}

/**
 * Get value for a property on a page.
 *
 * @param int $post_id
 * @param string $slug
 * @param mixed $default
 * @param string $type
 *
 * @deprecated deprecated for papi_get_field since version 2.0.0.
 *
 * @return mixed
 */

function papi_field( $post_id = null, $slug = null, $default = null, $type = 'post' ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'papi_get_field' );
	return papi_get_field( $post_id, $slug, $default, $type );
}
