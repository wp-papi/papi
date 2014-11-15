<?php

/**
 * Papi template functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add page type class name as a css class on body.
 *
 * @param array $classes
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_body_class($classes) {
	global $post;

	$page_type = get_post_meta( $post->ID, '__papi_page_type', true );

	if ( empty( $page_type ) ) {
		return $classes;
	}

	$classes[] = _papi_slugify( $page_type );

	return $classes;
}

add_filter( 'body_class', '_papi_body_class' );


/**
 * Include template files from Papis custom page template meta field.
 *
 * @param string $original_template
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_template_include( $original_template ) {
	global $post;

	if ( ! isset( $post ) || ! isset( $post->ID ) ) {
		return $original_template;
	}

	$page_template = _papi_get_page_type_template( $post->ID );

	if ( ! is_null( $page_template ) && ! empty( $page_template ) ) {
		$path = get_template_directory();
		$path = trailingslashit( $path );
		$file = $path . $page_template;

		if ( file_exists( $file ) && ! is_dir( $file ) ) {
			return $file;
		}
	}

	return $original_template;
}

add_filter( 'template_include', '_papi_template_include' );
