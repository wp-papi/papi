<?php

/**
 * Get the current page. Like in EPiServer.
 *
 * @deprecated deprecated since version 2.0.0.
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
 * You should use papi_get_field() instead.
 *
 * @param int    $post_id
 * @param string $slug
 * @param mixed  $default
 * @param string $type
 *
 * @deprecated deprecated since version 2.0.0.
 *
 * @return mixed
 */
function papi_field( $post_id = null, $slug = null, $default = null, $type = 'post' ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'papi_get_field' );
	return papi_get_field( $post_id, $slug, $default, $type );
}

/**
 * Get boxes with properties slug for a page.
 *
 * You should use papi_get_slugs() instead.
 *
 * @param int $post_id
 *
 * @deprecated deprecated since version 2.0.0.
 *
 * @return array
 */
function papi_fields( $post_id = 0 ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'papi_get_slugs' );
	return papi_get_slugs( $post_id );
}

/**
 * Get page type id.
 *
 * @param int $post_id
 *
 * @deprecated deprecated since version 2.0.0.
 *
 * @return string
 */
function papi_get_page_type_meta_value( $post_id = 0 ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'papi_get_page_type_id' );
	return papi_get_page_type_id( $post_id );
}
