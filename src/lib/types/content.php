<?php

/**
 * Check if content type exists.
 *
 * @param  string $id
 *
 * @return bool
 */
function papi_content_type_exists( $id ) {
	$page_types = papi_get_all_content_types();

	foreach ( $page_types as $page_type ) {
		if ( $page_type->match_id( $id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get all content types that exists.
 *
 * @param  array $args
 *
 * @return array
 */
function papi_get_all_content_types( array $args = [] ) {
	$default_args = [
		'all'   => true,
		'args'  => [],
		'mode'  => 'include',
		'types' => []
	];

	$args = array_merge( $default_args, $args );

	if ( ! is_array( $args['types'] ) ) {
		$args['types'] = [$args['types']];
	}

	if ( ! is_array( $args['args'] ) ) {
		$args['args'] = [$args['args']];
	}

	$args['args'] = array_filter( $args['args'] );

	if ( ! empty( $args['types'] ) ) {
		$args['all'] = false;
	}

	$content_types = [];
	$files         = papi()->once( __FUNCTION__, function () {
		return papi_get_all_core_type_files();
	} );

	foreach ( $files as $file ) {
		$content_type = papi_get_content_type( $file );

		if ( is_null( $content_type ) ) {
			continue;
		}

		// Only content types can be loaded.
		// @codeCoverageIgnoreStart
		if ( $content_type instanceof Papi_Content_Type === false ) {
			continue;
		}
		// @codeCoverageIgnoreEnd

		if ( $content_type->singleton() ) {
			if ( ! empty( $content_types ) ) {
				continue;
			}
		}

		$valid_type = in_array( $content_type->type, $args['types'] );
		$valid_type = $args['mode'] === 'include' ? $valid_type : ! $valid_type;

		if ( $args['all'] || ( $valid_type && call_user_func_array( [$content_type, 'allowed'], $args['args'] ) ) ) {
			$content_type->boot();
			$content_types[] = $content_type;
		}

		continue;
	}

	if ( is_array( $content_types ) ) {
		usort( $content_types, function ( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );
	}

	return papi_sort_order( array_reverse( $content_types ) );
}

/**
 * Get a content type by file path.
 *
 * @param  string $file_path
 *
 * @return null|Papi_Content_Type
 */
function papi_get_content_type( $file_path ) {
	if ( ! is_file( $file_path ) || ! is_string( $file_path ) ) {
		return;
	}

	$class_name = papi_get_class_name( $file_path );

	if ( empty( $class_name ) ) {
		return;
	}

	// Try to add the page type to the container.
	if ( ! papi()->exists( $class_name ) ) {

		// @codeCoverageIgnoreStart
		if ( ! class_exists( $class_name ) ) {
			require_once $file_path;
		}
		// @codeCoverageIgnoreEnd

		$rc         = new ReflectionClass( $class_name );
		$content_type  = $rc->newInstanceArgs( [$file_path] );

		// If the page type don't have a name we can't use it.
		if ( ! $content_type->has_name() ) {
			return;
		}

		papi()->singleton( $class_name, $content_type );
	}

	return papi()->make( $class_name );
}

/**
 * Get content type by identifier.
 *
 * @param  string $id
 *
 * @return Papi_content_type
 */
function papi_get_content_type_by_id( $id ) {
	if ( ! is_string( $id ) || empty( $id ) ) {
		return;
	}

	$result        = null;
	$content_types = papi_get_all_content_types();

	foreach ( $content_types as $content_type ) {
		if ( $content_type->match_id( $id ) ) {
			$result = $content_type;
			break;
		}
	}

	if ( is_null( $result ) ) {
		$path   = papi_get_file_path( $id );
		$result = papi_get_content_type( $path );
	}

	return $result;
}

/**
 * Get content type id.
 *
 * @return string
 */
function papi_get_content_type_id() {
	$content_type_id = papi_get_qs( 'content_type' );

	/**
	 * Change content type id.
	 *
	 * @param string $content_type_id
	 */
	return apply_filters( 'papi/content_type_id', $content_type_id );
}
