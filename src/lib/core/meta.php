<?php

/**
 * Get right meta id for meta type.
 *
 * @param  string $type
 *
 * @return string
 */
function papi_get_meta_id( $type = 'post' ) {
	$type = papi_get_meta_type( $type );
	return sprintf( '%s_id', $type );
}

/**
 * Get the data store.
 *
 * @param  int    $post_id
 * @param  string $type
 *
 * @return Papi_Core_Meta_Store|null
 */
function papi_get_meta_store( $post_id = 0, $type = 'post' ) {
	return Papi_Core_Meta_Store::factory( $post_id, $type );
}

/**
 * Get right meta type value.
 *
 * @param  string $type
 *
 * @return string
 */
function papi_get_meta_type( $type = 'post' ) {
	switch ( $type ) {
		case 'post':
		case 'page':
			return 'post';
		case 'taxonomy':
		case 'term':
			return 'term';
		default:
			return $type;
	}
}
