<?php

/**
 * Add page type class name as a css class on body.
 *
 * @param  array $classes
 *
 * @return array
 */
function papi_body_class( array $classes ) {
	global $post;

	// Check so we only change template on single and page posts.
	if ( ! is_single() && ! is_page() ) {
		return $classes;
	}

	$page_type = get_post_meta( $post->ID, papi_get_page_type_key(), true );

	if ( empty( $page_type ) ) {
		return $classes;
	}

	$parts = explode( '/', $page_type );

	if ( empty( $parts ) || empty( $parts[0] ) ) {
		return $classes;
	}

	$classes[] = array_pop( $parts );

	return $classes;
}

add_filter( 'body_class', 'papi_body_class' );

/**
 * Include partial view.
 *
 * @param string $file
 * @param array $vars
 */
function papi_include_template( $file, $vars = [] ) {
	if ( ! is_string( $file ) || empty( $file ) ) {
		return;
	}

	$path = PAPI_PLUGIN_DIR;
	$path = rtrim( $path, '/' ) . '/';

	if ( file_exists( $path . $file ) ) {
		require $path . $file;
	}
}

/**
 * Load a template array file and merge values with it.
 *
 * @param  string $file
 * @param  array $values
 * @param  bool $convert_to_object
 *
 * @return array
 */
function papi_template( $file, $values = [], $convert_to_object = false ) {
	if ( ! is_string( $file ) || empty( $file ) ) {
		return [];
	}

	$filepath = papi_get_file_path( $file );

	if ( empty( $filepath ) && is_file( $file ) ) {
		$filepath = $file;
	}

	if ( empty( $filepath ) || ! file_exists( $filepath ) || is_dir( $filepath ) ) {
		return [];
	}

	$template = require $filepath;

	if ( papi_is_property( $template ) ) {
		foreach ( $values as $key => $value ) {
			$template->set_option( $key, $value );
		}

		$result = $template;
	} else {
		$result = array_merge( (array) $template, $values );
	}

	if ( $convert_to_object ) {
		return (object) $result;
	}

	return $result;
}

/**
 * Include template files from Papis custom page template meta field.
 *
 * @param  string $original_template
 *
 * @return string
 */
function papi_template_include( $original_template ) {
	global $post;

	// Check so we only change template on single and page posts.
	if ( ! is_single() && ! is_page() ) {
		return $original_template;
	}

	// Only load a template if it exists.
	if ( $page_template = papi_get_page_type_template( $post->ID ) ) {
		if ( $template = locate_template( $page_template ) ) {
			return $template;
		}
	}

	return $original_template;
}

add_filter( 'template_include', 'papi_template_include' );
