<?php

/**
 * Add page type class name as a css class on body.
 *
 * @param  array $classes
 *
 * @return array
 */
function papi_body_class( array $classes ) {
	$classes = array_merge( $classes, papi_get_entry_type_body_classes() );

	if ( $class = papi_get_entry_type_css_class() ) {
		$classes[] = $class;
	}

	return array_unique( $classes );
}
add_filter( 'body_class', 'papi_body_class' );

/**
 * Get template file name.
 *
 * @param  string $template
 *
 * @return string|null
 */
function papi_get_template_file_name( $template ) {
	if ( ! is_string( $template ) ) {
		return;
	}

	$extension = apply_filters( 'papi/template_extension', '.php' );
	$ext_reg   = '/(' . $extension . ')+$/';

	if ( preg_match( '/\.\w+$/', $template, $matches ) && preg_match( $ext_reg, $matches[0] ) ) {
		return str_replace( '.', '/', preg_replace( '/' . $matches[0] . '$/', '', $template ) ) . $matches[0];
	}

	$template = str_replace( '.', '/', $template );
	$template = substr( $template, -strlen( $extension ) ) === $extension ? $template : $template . $extension;

	return $template === $extension ? null : $template;
}

/**
 * Include partial view.
 *
 * @param string $file
 * @param array $vars
 */
function papi_include_template( $file, array $vars = [] ) {
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
 * @param  array  $values
 * @param  bool   $convert_to_object
 *
 * @return array|object
 */
function papi_template( $file, array $values = [], $convert_to_object = false ) {
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
	if ( ! is_single() && ! is_page() && ! is_tag() ) {
		return $original_template;
	}

	// Determine which id to use.
	$id = is_tag() ? get_queried_object()->term_id : $post->ID;

	// Only load a template if it exists.
	if ( $page_template = papi_get_entry_type_template( $id ) ) {
		if ( $template = locate_template( $page_template ) ) {
			/**
			 * Change which template that is used by Papi.
			 *
			 * @param  string $template
			 *
			 * @return string
			 */
			return apply_filters( 'papi/template_include', $template );
		}
	}

	return $original_template;
}
add_filter( 'template_include', 'papi_template_include' );
