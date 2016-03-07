<?php

/**
 * Count entry types in the database for the
 * given entry type.
 *
 * @param  string|Papi_Entry_Type $entry_type
 *
 * @return int
 */
function papi_get_entry_type_count( $entry_type ) {
	global $wpdb;

	if ( empty( $entry_type ) || ( ! is_string( $entry_type ) && ( ! is_object( $entry_type ) ) ) ) {
		return 0;
	}

	if ( is_string( $entry_type ) ) {
		$entry_type = papi_get_entry_type_by_id( $entry_type );
	}

	if ( $entry_type instanceof Papi_Entry_Type === false ) {
		return 0;
	}

	$table = sprintf( '%s%smeta', $wpdb->prefix, papi_get_meta_type( $entry_type->type ) );
	$sql   = "SELECT COUNT(*) FROM $table WHERE `meta_key` = '%s' AND `meta_value` = '%s'";
	$sql   = $wpdb->prepare( $sql, papi_get_page_type_key(), $entry_type->get_id() );

	return intval( $wpdb->get_var( $sql ) );
}

/**
 * Check if entry type exists.
 *
 * @param  string $id
 *
 * @return bool
 */
function papi_entry_type_exists( $id ) {
	$page_types = papi_get_all_entry_types();

	foreach ( $page_types as $page_type ) {
		if ( $page_type->match_id( $id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get all entry types that exists.
 *
 * @param  array $args {
 *   @type bool         $all
 *   @type mixed        $args
 *   @type array|string $id
 *   @type string       $mode
 *   @type array|string $types
 * }
 *
 * @return array
 */
function papi_get_all_entry_types( array $args = [] ) {
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

	$entry_types = [];
	$files       = papi_get_all_core_type_files();

	foreach ( $files as $file ) {
		$entry_type = papi_get_entry_type( $file );

		if ( is_null( $entry_type ) ) {
			continue;
		}

		// Only entry types can be loaded.
		// @codeCoverageIgnoreStart
		if ( $entry_type instanceof Papi_Entry_Type === false ) {
			continue;
		}
		// @codeCoverageIgnoreEnd

		if ( $entry_type->singleton() ) {
			if ( ! empty( $entry_types ) ) {
				continue;
			}
		}

		$id         = $entry_type->get_id();
		$valid_type = in_array( $entry_type->type, $args['types'] );
		$valid_type = $args['mode'] === 'include' ? $valid_type : ! $valid_type;

		if ( $args['all'] || ( $valid_type && call_user_func_array( [$entry_type, 'allowed'], $args['args'] ) ) ) {
			$entry_type->boot();
			$entry_types[$id] = $entry_type;
		}
	}

	$entry_types = array_values( $entry_types );

	if ( is_array( $entry_types ) ) {
		usort( $entry_types, function ( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );
	}

	return papi_sort_order( array_reverse( $entry_types ) );
}

/**
 * Get a entry type by file path.
 *
 * @param  string $file_path
 *
 * @return null|Papi_entry_type
 */
function papi_get_entry_type( $file_path ) {
	if ( ! is_file( $file_path ) || ! is_string( $file_path ) ) {
		return;
	}

	$file_path  = strtolower( $file_path );
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

		if ( ! class_exists( $class_name ) ) {
			return;
		}
		// @codeCoverageIgnoreEnd

		$rc         = new ReflectionClass( $class_name );
		$entry_type = $rc->newInstanceArgs( [$file_path] );

		// If the page type don't have a name we can't use it.
		if ( ! $entry_type->has_name() ) {
			return;
		}

		papi()->singleton( $class_name, $entry_type );
	}

	return papi()->make( $class_name );
}

/**
 * Get entry type by identifier.
 *
 * @param  string $id
 *
 * @return Papi_Entry_Type
 */
function papi_get_entry_type_by_id( $id ) {
	if ( ! is_string( $id ) || empty( $id ) ) {
		return;
	}

	$result        = null;
	$entry_types = papi_get_all_entry_types();

	foreach ( $entry_types as $entry_type ) {
		if ( $entry_type->match_id( $id ) ) {
			$result = $entry_type;
			break;
		}
	}

	if ( is_null( $result ) ) {
		$path   = papi_get_file_path( $id );
		$result = papi_get_entry_type( $path );
	}

	return $result;
}

/**
 * Get entry type from meta id.
 *
 * @param  int    $id
 * @param  string $type
 *
 * @return Papi_Entry_Type
 */
function papi_get_entry_type_by_meta_id( $id = 0, $type = 'post' ) {
	if ( ! is_numeric( $id ) ) {
		return;
	}

	$type = papi_get_meta_type( $type );
	$id   = papi_get_meta_id( $type, $id );

	if ( $id === 0 ) {
		return;
	}

	if ( $entry_type = papi_get_entry_type_id( $id, $type === 'post' ? null : $type ) ) {
		return papi_get_entry_type_by_id( $entry_type );
	}
}

/**
 * Get entry type id.
 *
 * @param  int $id
 * @param  string $type
 *
 * @return string
 */
function papi_get_entry_type_id( $id = 0, $type = null ) {
	$type = papi_get_meta_type( $type );
	$id   = papi_get_meta_id( $type, $id );

	if ( $id > 0 ) {
		if ( $meta_value = get_metadata( $type, $id, papi_get_page_type_key(), true ) ) {
			return $meta_value;
		}
	}

	$entry_type_id = papi_get_qs( 'entry_type' );

	/**
	 * Change entry type id.
	 *
	 * @param string $entry_type_id
	 * @param string $type
	 */
	return apply_filters( 'papi/entry_type_id', $entry_type_id, $type );
}

/**
 * Get entry template file from meta id.
 *
 * @param  int    $id
 * @param  string $type
 *
 * @return null|string
 */
function papi_get_entry_type_template( $id = 0, $type = 'post' ) {
	if ( empty( $id ) && ! is_numeric( $id ) ) {
		return;
	}

	$data = papi_get_entry_type_by_meta_id( $id, $type );

	if ( isset( $data, $data->template ) ) {
		return papi_get_template_file_name( $data->template );
	}
}
