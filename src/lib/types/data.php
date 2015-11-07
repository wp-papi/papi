<?php

/**
 * Check if page type exists.
 *
 * @param  string $id
 *
 * @return bool
 */
function papi_data_type_exists( $id ) {
	$page_types = papi_get_all_data_types();

	foreach ( $page_types as $page_type ) {
		if ( $page_type->match_id( $id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get all data types that exists.
 *
 * @param  array $args
 *
 * @return array
 */
function papi_get_all_data_types( array $args = [] ) {
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

	$data_types = [];
	$load_once  = papi_filter_core_load_one_type_on();
	$files      = papi_get_all_core_type_files();

	foreach ( $files as $file ) {
		$data_type = papi_get_data_type( $file );

		if ( is_null( $data_type ) ) {
			continue;
		}

		// Only data types can be loaded.
		if ( $data_type instanceof Papi_Data_Type === false ) {
			continue;
		}

		if ( $data_type->singleton() ) {
			if ( ! empty( $data_types ) ) {
				continue;
			}
		}

		$valid_type = in_array( $data_type->type, $args['types'] );
		$valid_type = $args['mode'] === 'include' ? $valid_type : ! $valid_type;

		if ( $args['all'] || ( $valid_type && call_user_func_array( [$data_type, 'allowed'], $args['args'] ) ) ) {
			$data_type->boot();
			$data_types[] = $data_type;
		}

		continue;
	}

	if ( is_array( $data_types ) ) {
		usort( $data_types, function ( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );
	}

	return papi_sort_order( array_reverse( $data_types ) );
}

/**
 * Get a data type by file path.
 *
 * @param  string $file_path
 *
 * @return Papi_Data_Type
 */
function papi_get_data_type( $file_path ) {
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
		$data_type  = $rc->newInstanceArgs( [$file_path] );

		// If the page type don't have a name we can't use it.
		if ( ! $data_type->has_name() ) {
			return;
		}

		papi()->singleton( $class_name, $data_type );
	}

	return papi()->make( $class_name );
}

/**
 * Get data type by identifier.
 *
 * @param  string $id
 *
 * @return Papi_Data_Type
 */
function papi_get_data_type_by_id( $id ) {
	if ( ! is_string( $id ) || empty( $id ) ) {
		return;
	}

	$result     = null;
	$data_types = papi_get_all_data_types();

	foreach ( $data_types as $data_type ) {
		if ( $data_type->match_id( $id ) ) {
			$result = $data_type;
			break;
		}
	}

	if ( is_null( $result ) ) {
		$path   = papi_get_file_path( $id );
		$result = papi_get_data_type( $path );
	}

	return $result;
}

/**
 * Get page type id.
 *
 * @param  int $post_id
 *
 * @return string
 */
function papi_get_data_type_id( $post_id = 0 ) {
	$post_id   = papi_get_post_id( $post_id );
	$key       = papi_get_page_type_key();
	$data_type = '';

	if ( $post_id !== 0 ) {
		$meta_value = get_post_meta( $post_id, $key, true );
		$data_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	if ( empty( $data_type ) ) {
		$data_type = str_replace( 'papi/', '', papi_get_qs( 'page_type' ) );
	}

	if ( empty( $data_type ) ) {
		$data_type = papi_get_sanitized_post( $key );
	}

	// Load right page type from a post query string
	if ( empty( $data_type ) ) {
		$meta_value = get_post_meta( papi_get_parent_post_id(), $key, true );
		$data_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	// Load page type id from the container if it exists or
	// load it from `papi_get_all_data_types`.
	if ( empty( $data_type ) ) {
		$post_type      = papi_get_post_type();
		$load_once      = papi_filter_core_load_one_type_on();
		$collection_key = 'core.data_type.' . $post_type;

		if ( in_array( $post_type, $load_once ) ) {
			if ( papi()->exists( $collection_key )  ) {
				return papi()->make( $collection_key );
			}

			if ( $data_types = papi_get_all_page_types( $post_type ) ) {
				return $data_types[0]->get_id();
			}
		}
	}

	return $data_type;
}
