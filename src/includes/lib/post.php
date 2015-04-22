<?php

/**
 * Papi post functions.
 *
 * @package Papi
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Get post or page id from a object.
 *
 * @param mixed $post_id
 *
 * @since 1.0.0
 *
 * @return int
 */

function papi_get_post_id( $post_id = null ) {
	// If it's a post object we can return the id from it.
	if ( is_object( $post_id ) ) {
		return $post_id->ID;
	}

	// If it's not null and it's a numeric string we can convert it to int and return it.
	if ( is_numeric( $post_id ) && is_string( $post_id ) ) {
		return intval( $post_id );
	}

	if ( is_null( $post_id ) ) {
		// If `get_post` function is available and post id is null we can return the post id.
		if ( get_post() ) {
			return get_the_ID();
		}

		// If the post id is null and post query string is available we can return it as post id.
		if ( isset( $_GET['post'] ) ) {
			return intval( $_GET['post'] );
		}

		// If the post id is null and page id query string is available we can return it as post id.
		if ( isset( $_GET['page_id'] ) ) {
			return intval( $_GET['page_id'] );
		}
	}

	// Or return null or the given value of post id.
	return $post_id;
}

/**
 * Get WordPress post type in various ways
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_get_wp_post_type() {
	if ( isset( $_GET['post_type'] ) ) {
		return esc_html( strtolower( $_GET['post_type'] ) );
	}

	if ( isset( $_POST['post_type'] ) ) {
		return esc_html( strtolower( $_POST['post_type'] ) );
	}

	$post_id = papi_get_post_id();

	if ( $post_id != 0 ) {
		return strtolower( get_post_type( $post_id ) );
	}

	$page = papi_get_qs( 'page' );

	if ( strpos( strtolower( $page ), 'papi-add-new-page,' ) !== false ) {
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
