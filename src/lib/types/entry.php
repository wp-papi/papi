<?php

/**
 * Get entry type body classes.
 *
 * @param  int    $id
 * @param  string $type
 *
 * @return string
 */
function papi_get_entry_type_body_classes( $id = 0, $type = null ) {
	$entry_type_id = papi_get_entry_type_id( $id, $type );

	if ( empty( $entry_type_id ) ) {
		return [];
	}

	$entry_type = papi_get_entry_type_by_id( $entry_type_id );

	if ( $entry_type instanceof Papi_Entry_Type === false ) {
		return [];
	}

	$classes = $entry_type->body_classes();
	$classes = is_array( $classes ) ? $classes : [];

	return $classes;
}

/**
 * Get entry type css class, it will split the entry type id
 * on slash and take the last part of the id.
 *
 * @param  int    $id
 * @param  string $type
 *
 * @return string
 */
function papi_get_entry_type_css_class( $id = 0, $type = null ) {
	$entry_type = papi_get_entry_type_id( $id, $type );

	if ( empty( $entry_type ) ) {
		return '';
	}

	$parts = explode( '/', $entry_type );

	if ( empty( $parts ) || empty( $parts[0] ) ) {
		return '';
	}

	return array_pop( $parts );
}

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

	// @codingStandardsIgnoreStart
	$sql = $wpdb->prepare(
		"SELECT COUNT(*) FROM `$table` WHERE `meta_key` = '%s' AND `meta_value` = '%s'",
		papi_get_page_type_key(),
		$entry_type->get_id()
	);

	return intval( $wpdb->get_var( $sql ) );
	// @codingStandardsIgnoreEnd
}

/**
 * Check if entry type exists.
 *
 * @param  string $id
 *
 * @return bool
 */
function papi_entry_type_exists( $id ) {
	if ( papi()->exists( $id ) ) {
		return true;
	}

	$entry_types = papi_get_all_entry_types();

	foreach ( $entry_types as $entry_type ) {
		if ( $entry_type->match_id( $id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get all entry types that exists.
 *
 * @param  array $args {
 *     @type bool         $all
 *     @type mixed        $args
 *     @type bool         $cache
 *     @type array|string $id
 *     @type string       $mode
 *     @type array|string $types
 * }
 *
 * @return array
 */
function papi_get_all_entry_types( array $args = [] ) {
	$default_args = [
		'all'   => true,
		'args'  => [],
		'cache' => true,
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

	$cache_key = papi_cache_key( __FUNCTION__, md5( serialize( $args ) ) );

	if ( ! $args['cache'] ) {
		papi()->remove( 'papi_get_all_core_type_files' );
		papi()->remove( $cache_key );
	}

	if ( papi()->exists( $cache_key ) ) {
		if ( $entry_types = papi()->make( $cache_key ) ) {
			return $entry_types;
		}

		papi()->remove( $cache_key );
		papi()->remove( 'papi_get_all_core_type_files' );
	}

	$singletons  = [];
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
			if ( isset( $singletons[$entry_type->type] ) ) {
				continue;
			} else {
				$singletons[$entry_type->type] = true;
			}
		}

		$id         = $entry_type->get_id();
		$valid_type = in_array( $entry_type->type, $args['types'], true );
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

	$entry_types = papi_sort_order( array_reverse( $entry_types ) );

	if ( ! papi()->exists( $cache_key ) ) {
		papi()->singleton( $cache_key, $entry_types );
	}

	return $entry_types;
}

/**
 * Get a entry type by file path.
 *
 * @param  string $file_path
 *
 * @return Papi_Entry_Type
 */
function papi_get_entry_type( $file_path ) {
	if ( ! is_file( $file_path ) || ! is_string( $file_path ) ) {
		return;
	}

	$id = papi_get_core_type_base_path( $file_path );

	// Check if file path is changed.
	if ( $id === $file_path ) {
		return;
	}

	if ( ! papi()->exists( $id ) ) {
		$class_name = papi_get_class_name( $file_path );

		// @codeCoverageIgnoreStart
		if ( ! class_exists( $class_name ) ) {
			require_once $file_path;
		}

		if ( ! class_exists( $class_name ) ) {
			return;
		}
		// @codeCoverageIgnoreEnd

		$rc = new ReflectionClass( $class_name );

		// Bail if not instantiable.
		if ( ! $rc->isInstantiable() ) {
			return;
		}

		$entry_type = $rc->newInstanceArgs( [$file_path] );

		// If the entry type don't have a name we can't use it.
		if ( ! $entry_type->has_name() ) {
			return;
		}

		papi()->singleton( $id, $entry_type );
	}

	return papi()->make( $id );
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

	if ( papi()->exists( $id ) ) {
		$value = papi()->make( $id );

		if ( $value instanceof Papi_Entry_Type ) {
			return $value;
		}
	}

	$result      = null;
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

	// Check for class prefixed id when a old id that don't have the class prefix.
	if ( is_null( $result ) && strpos( $id, 'class-' ) === false ) {
		$parts = explode( '/', $id );
		$parts[count( $parts ) - 1] = 'class-' . $parts[count( $parts ) - 1];
		$result = papi_get_entry_type_by_id( implode( '/', $parts ) );
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
function papi_get_entry_type_by_meta_id( $id = 0, $type = null ) {
	if ( ! is_numeric( $id ) ) {
		return;
	}

	$type = papi_get_meta_type( $type );
	$id   = papi_get_meta_id( $type, $id );

	if ( $id === 0 ) {
		return;
	}

	if ( $entry_type = papi_get_entry_type_id( $id, $type ) ) {
		return papi_get_entry_type_by_id( $entry_type );
	}
}

/**
 * Get entry type id.
 *
 * @param  int    $id
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
	 * @param  string $entry_type_id
	 * @param  string $type
	 * @param  int    $id
	 *
	 * @return string
	 */
	return apply_filters( 'papi/entry_type_id', $entry_type_id, $type, $id );
}

/**
 * Get entry template file from meta id.
 *
 * @param  int    $id
 * @param  string $type
 *
 * @return null|string
 */
function papi_get_entry_type_template( $id = 0, $type = null ) {
	if ( empty( $id ) && ! is_numeric( $id ) ) {
		return;
	}

	$data = papi_get_entry_type_by_meta_id( $id, $type );

	if ( isset( $data, $data->template ) ) {
		return papi_get_template_file_name( $data->template );
	}
}

/**
 * Check if given string is a entry type.
 *
 * @param  string $str
 *
 * @return bool
 */
function papi_is_entry_type( $str = '' ) {
	$id = papi_get_entry_type_id();

	return empty( $str ) ? ! empty( $id ) : $str === $id;
}
