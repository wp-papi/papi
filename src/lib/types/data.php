<?php

/**
 * Check if page type exists.
 *
 * @param  string $id
 *
 * @return bool
 */
function papi_data_type_exists( $id ) {
	$exists     = false;
	$page_types = papi_get_all_data_types( true );

	foreach ( $page_types as $page_type ) {
		if ( $page_type->match_id( $id ) ) {
			$exists = true;
			break;
		}
	}

	return $exists;
}

/**
 * Get all data types that exists.
 *
 * @param  bool   $all
 * @param  string $post_type
 * @param  bool   $fake_post_types
 *
 * @return array
 */
function papi_get_all_data_types( $all = false, $post_type = null, $fake_post_types = false ) {
	if ( empty( $post_type ) ) {
		$post_type  = papi_get_post_type();
	}

	$cache_key  = papi_cache_key( sprintf( '%s_%s', $all, $post_type ), $fake_post_types );
	$data_types = wp_cache_get( $cache_key );
	$load_once  = papi_filter_core_load_one_type_on();

	if ( empty( $data_types ) ) {
		$files = papi_get_all_data_type_files();

		foreach ( $files as $file ) {
			$data_type = papi_get_data_type( $file );

			if ( is_null( $data_type ) ) {
				continue;
			}

			// Only core data types can be loaded.
			if ( $data_type instanceof Papi_Data_Type === false ) {
				continue;
			}

			// Not all data types has a post type.
			if ( ! isset( $data_type->post_type ) ) {
				// If the
				if ( $fake_post_types ) {
					// Boot page type.
					$data_type->boot();

					// Add it to the page types array.
					$data_types[] = $data_type;
				}

				continue;
			}

			// No page type or a fake post type loading can not be continued.
			if ( ! papi_is_page_type( $data_type ) || $fake_post_types ) {
				continue;
			}

			if ( papi()->exists( 'core.data_type.' . $data_type->post_type[0] ) ) {
				if ( ! empty( $data_types ) ) {
					continue;
				}
			} else if ( in_array( $data_type->post_type[0], $load_once ) ) {
				papi()->singleton( 'core.data_type.' . $data_type->post_type[0], $data_type->get_id() );
			}

			// Add the page type if the post types is allowed.
			if ( $all || $data_type->allowed( $post_type ) ) {
				// Boot page type.
				$data_type->boot();

				// Add it to the page types array.
				$data_types[] = $data_type;
			}
		}

		if ( is_array( $data_types ) ) {
			usort( $data_types, function ( $a, $b ) {
				return strcmp( $a->name, $b->name );
			} );

			wp_cache_set( $cache_key, $data_types );
		}
	}

	if ( ! is_array( $data_types ) ) {
		return [];
	}

	if ( $fake_post_types ) {
		return $data_types;
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
	$data_types = papi_get_all_data_types( true );

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

			if ( $data_types = papi_get_all_data_types( false, $post_type ) ) {
				return $data_types[0]->get_id();
			}
		}
	}

	return $data_type;
}

/**
 * Get the page type key that is used for each data type.
 *
 * @return string
 */
function papi_get_data_type_key() {
	return '_papi_data_type';
}

/**
 * Check if `$obj` is a instanceof `Papi_Option_Type`.
 *
 * @param  mixed $obj
 *
 * @return bool
 */
function papi_is_option_type( $obj ) {
	return $obj instanceof Papi_Option_Type;
}

/**
 * Check if `$obj` is a instanceof `Papi_Page_Type`.
 *
 * @param  mixed $obj
 *
 * @return bool
 */
function papi_is_page_type( $obj ) {
	return $obj instanceof Papi_Page_Type;
}
