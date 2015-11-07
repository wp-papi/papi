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
	$files         = papi_get_all_core_type_files();

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

	$result     = null;
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
 * @param  int $post_id
 *
 * @return string
 */
function papi_get_content_type_id( $post_id = 0 ) {
	$post_id   = papi_get_post_id( $post_id );
	$key       = papi_get_page_type_key();
	$content_type = '';

	if ( $post_id !== 0 ) {
		$meta_value = get_post_meta( $post_id, $key, true );
		$content_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	if ( empty( $content_type ) ) {
		$content_type = str_replace( 'papi/', '', papi_get_qs( 'page_type' ) );
	}

	if ( empty( $content_type ) ) {
		$content_type = papi_get_sanitized_post( $key );
	}

	// Load right page type from a post query string
	if ( empty( $content_type ) ) {
		$meta_value = get_post_meta( papi_get_parent_post_id(), $key, true );
		$content_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	// Load page type id from the container if it exists or
	// load it from `papi_get_all_page_types`.
	if ( empty( $content_type ) ) {
		$post_type      = papi_get_post_type();
		$collection_key = 'core.content_type.' . $post_type;

		if ( $post_type != 'attachment' ) {
			return $content_type;
		}

		if ( papi()->exists( $collection_key )  ) {
			return papi()->make( $collection_key );
		}

		if ( $page_types = papi_get_all_page_types( $post_type ) ) {
			return $page_types[0]->get_id();
		}
	}

	return $content_type;
}
