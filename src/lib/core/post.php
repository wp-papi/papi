<?php

/**
 * Get post or page id from a object.
 *
 * @param  mixed $post_id
 *
 * @return int
 */
function papi_get_post_id( $post_id = null ) {
	if ( is_object( $post_id ) && isset( $post_id->ID ) ) {
		return $post_id->ID;
	}

	if ( is_numeric( $post_id ) && is_string( $post_id ) && $post_id !== '0' ) {
		return intval( $post_id );
	}

	if ( is_null( $post_id ) || intval( $post_id ) === 0 ) {
		if ( isset( $_POST['action'] ) ) {
			if ( $_POST['action'] === 'query-attachments' && isset( $_POST['query']['item'] ) ) {
				return intval( $_POST['query']['item'] );
			}
		}

		if ( get_post() ) {
			return get_the_ID();
		}

		if ( $value = papi_get_qs( 'post' ) ) {
			return is_array( $value ) ? 0 : intval( $value );
		}

		if ( $value = papi_get_qs( 'page_id' ) ) {
			return intval( $value );
		}

		return intval( $post_id );
	}

	return intval( $post_id );
}

/**
 * Get post parent id.
 *
 * @return int
 */
function papi_get_parent_post_id() {
	return intval( papi_get_qs( 'post_parent' ) );
}

/**
 * Get WordPress post type in various ways.
 *
 * @param  int $post_id
 *
 * @return string
 */
function papi_get_post_type( $post_id = null ) {
	if ( $post_type = papi_get_or_post( 'post_type' ) ) {
		return $post_type;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id !== 0 ) {
		return strtolower( get_post_type( $post_id ) );
	}

	$page = papi_get_qs( 'page' );

	if ( is_string( $page ) && strpos( strtolower( $page ), 'papi-add-new-page,' ) !== false ) {
		$exploded = explode( ',', $page );

		if ( empty( $exploded[1] ) ) {
			return '';
		}

		return $exploded[1];
	}

	// If only `post-new.php` without any querystrings
	// it would be the post post type.
	$req_uri  = $_SERVER['REQUEST_URI'];
	$exploded = explode( '/', $req_uri );
	$last     = end( $exploded );

	if ( $last === 'post-new.php' ) {
		return 'post';
	}

	return '';
}

/**
 * Get post type label.
 *
 * @param  string $post_type
 * @param  string $label
 * @param  string $default
 *
 * @return string
 */
function papi_get_post_type_label( $post_type, $label, $default = '' ) {
	if ( ! post_type_exists( $post_type ) ) {
		return $default;
	}

	return get_post_type_object( $post_type )->labels->$label;
}
