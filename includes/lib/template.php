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

function _papi_body_class( $classes ) {
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
 * Load a template array file and merge values with it.
 *
 * @param string $file
 * @param array $values
 * @param bool $convert_to_object
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_template( $file, $values = array(), $convert_to_object = false ) {
	if ( ! is_string( $file ) ) {
		return array();
	}

	$filepath = _papi_get_file_path( $file );

	if ( empty( $filepath ) && is_file( $file ) ) {
		$filepath = $file;
	}

	if ( ! is_file( $filepath ) || empty( $filepath ) ) {
		return array();
	}

	$template = require $filepath;

	$result = array_merge( (array)$template, $values );

	if ( $convert_to_object ) {
		return (object) $result;
	}

	return $result;
}

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
