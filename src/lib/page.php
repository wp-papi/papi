<?php

/**
 * Check if the page type should be displayed or not.
 *
 * @param  string|object $page_type
 *
 * @return bool
 */
function papi_display_page_type( $page_type ) {
	$post_type = papi_get_post_type();

	if ( empty( $post_type ) ) {
		return false;
	}

	if ( is_string( $page_type ) ) {
		$page_type = papi_get_page_type_by_id( $page_type );
	}

	if ( ! is_object( $page_type ) ) {
		return false;
	}

	if ( ! in_array( $post_type, $page_type->post_type ) ) {
		return false;
	}

	$display = $page_type->display( $post_type );

	if ( ! is_bool( $display ) || $display === false ) {
		return false;
	}

	if ( preg_match( '/papi\-standard\-\w+\-type/', $page_type->get_id() ) ) {
		return true;
	}

	$parent_page_type = papi_get_page_type_by_post_id( papi_get_parent_post_id() );

	if ( papi_is_page_type( $parent_page_type ) ) {
		$child_types = $parent_page_type->get_child_types();

		if ( ! empty( $child_types ) ) {
			return in_array( $page_type, $parent_page_type->get_child_types() );
		}
	}

	// Run show page type filter.
	return papi_filter_settings_show_page_type( $post_type, $page_type );
}

/**
 * Get all page types that exists.
 *
 * @param  bool   $all
 * @param  string $post_type
 * @param  bool   $fake_post_types
 *
 * @return array
 */
function papi_get_all_page_types( $all = false, $post_type = null, $fake_post_types = false ) {
	if ( empty( $post_type ) ) {
		$post_type  = papi_get_post_type();
	}

	$cache_key   = papi_cache_key( sprintf( '%s_%s', $all, $post_type ), $fake_post_types );
	$page_types  = wp_cache_get( $cache_key );
	$load_once   = papi_filter_core_load_one_type_on();

	if ( empty( $page_types ) ) {
		$files = papi_get_all_page_type_files();

		foreach ( $files as $file ) {
			$page_type = papi_get_page_type( $file );

			if ( is_null( $page_type ) ) {
				continue;
			}

			if ( $page_type instanceof Papi_Page_Type === false ) {
				continue;
			}

			if ( papi()->exists( 'core.page_type.' . $page_type->post_type[0] ) ) {
				if ( ! empty( $page_types ) ) {
					continue;
				}
			} else if ( in_array( $page_type->post_type[0], $load_once ) ) {
				papi()->singleton( 'core.page_type.' . $page_type->post_type[0], $page_type->get_id() );
			}

			if ( $fake_post_types ) {
				if ( isset( $page_type->post_type[0] ) && ! post_type_exists( $page_type->post_type[0] ) ) {
					// Boot page type.
					$page_type->boot();

					// Add it to the page types array.
					$page_types[] = $page_type;
				}
				continue;
			} else if ( $page_type instanceof Papi_Option_Type ) {
				continue;
			}

			// Add the page type if the post types is allowed.
			if ( ! is_null( $page_type ) && papi_current_user_is_allowed( $page_type->capabilities ) && ( $all || in_array( $post_type, $page_type->post_type ) ) ) {
				// Boot page type.
				$page_type->boot();

				// Add it to the page types array.
				$page_types[] = $page_type;
			}
		}

		if ( is_array( $page_types ) ) {
			usort( $page_types, function ( $a, $b ) {
				return strcmp( $a->name, $b->name );
			} );

			wp_cache_set( $cache_key, $page_types );
		}
	}

	if ( ! is_array( $page_types ) ) {
		return [];
	}

	return papi_sort_order( array_reverse( $page_types ) );
}

/**
 * Get number of how many pages uses the given page type.
 * This will also work with only page type.
 *
 * @param  string|object $page_type
 *
 * @return int
 */
function papi_get_number_of_pages( $page_type ) {
	global $wpdb;

	if ( empty( $page_type ) || ( ! is_string( $page_type ) && ( ! is_object( $page_type ) ) ) ) {
		return 0;
	}

	if ( is_object( $page_type ) && method_exists( $page_type, 'get_id' ) ) {
		$page_type = $page_type->get_id();
	}

	if ( ! is_string( $page_type ) ) {
		return 0;
	}

	$value = papi_cache_get( 'page_type', $page_type );

	if ( $value === false ) {
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE `meta_key` = '%s' AND `meta_value` = '%s'";
		$sql = $wpdb->prepare( $sql, papi_get_page_type_key(), $page_type );

		$value = intval( $wpdb->get_var( $sql ) );
		papi_cache_set( 'page_type', $page_type, $value );
	}

	return $value;
}

/**
 * Get the data page.
 *
 * @param  int    $post_id
 * @param  string $type
 *
 * @return Papi_Core_Page|null
 */
function papi_get_page( $post_id = 0, $type = 'post' ) {
	return Papi_Core_Page::factory( $post_id, $type );
}

/**
 * Get a page type by file path.
 *
 * @param  string $file_path
 *
 * @return Papi_Page_Type
 */
function papi_get_page_type( $file_path ) {
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
		$page_type  = $rc->newInstanceArgs( [$file_path] );

		// If the page type don't have a name we can't use it.
		if ( ! $page_type->has_name() ) {
			return;
		}

		papi()->singleton( $class_name, $page_type );
	}

	return papi()->make( $class_name );
}

/**
 * Get page type by identifier.
 *
 * @param  string $id
 *
 * @return Papi_Page_Type
 */
function papi_get_page_type_by_id( $id ) {
	if ( ! is_string( $id ) || empty( $id ) ) {
		return;
	}

	$result     = null;
	$page_types = papi_get_all_page_types( true );

	foreach ( $page_types as $page_type ) {
		if ( $page_type->match_id( $id ) ) {
			$result = $page_type;
			break;
		}
	}

	if ( is_null( $result ) ) {
		$path   = papi_get_file_path( $id );
		$result = papi_get_page_type( $path );
	}

	return $result;
}

/**
 * Get page type from post id.
 *
 * @param  int $post_id
 *
 * @return Papi_Page_Type
 */
function papi_get_page_type_by_post_id( $post_id = 0 ) {
	if ( ! is_numeric( $post_id ) ) {
		return;
	}

	$post_id = papi_get_post_id( $post_id );

	if ( $post_id === 0 ) {
		return;
	}

	if ( $page_type = papi_get_page_type_id( $post_id ) ) {
		return papi_get_page_type_by_id( $page_type );
	}
}

/**
 * Get page type id.
 *
 * @param  int $post_id
 *
 * @return string
 */
function papi_get_page_type_id( $post_id = 0 ) {
	$post_id   = papi_get_post_id( $post_id );
	$key       = papi_get_page_type_key();
	$page_type = '';

	if ( $post_id !== 0 ) {
		$meta_value = get_post_meta( $post_id, $key, true );
		$page_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	if ( empty( $page_type ) ) {
		$page_type = str_replace( 'papi/', '', papi_get_qs( 'page_type' ) );
	}

	if ( empty( $page_type ) ) {
		$page_type = papi_get_sanitized_post( papi_get_page_type_key() );
	}

	// Load right page type from a post query string
	if ( empty( $page_type ) ) {
		$meta_value = get_post_meta( papi_get_parent_post_id(), $key, true );
		$page_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	// When using `only_page_type` filter we need to fetch the value since it
	// maybe not always saved in the database.
	if ( empty ( $page_type ) ) {
		$post_type = get_post_type( $post_id );

		if ( is_string( $post_type ) && $page_type = papi_filter_settings_only_page_type( $post_type ) ) {
			return $page_type;
		}
	}

	// Load page type id from the container if it exists or
	// load it from `papi_get_all_page_types`.
	if ( empty( $page_type ) ) {
		$post_type      = papi_get_post_type();
		$load_once      = papi_filter_core_load_one_type_on();
		$collection_key = 'core.page_type.' . $post_type;

		if ( in_array( $post_type, $load_once ) ) {
			if ( papi()->exists( $collection_key )  ) {
				return papi()->make( $collection_key );
			}

			if ( $page_types = papi_get_all_page_types( false, $post_type ) ) {
				return $page_types[0]->get_id();
			}
		}
	}

	return $page_type;
}

/**
 * Get the page type key that is used for each post.
 *
 * @return string
 */
function papi_get_page_type_key() {
	return defined( 'PAPI_PAGE_TYPE_KEY' ) ? PAPI_PAGE_TYPE_KEY : '_papi_page_type';
}

/**
 * Get the Page type name.
 *
 * @param  int $post_id
 *
 * @return string
 */
function papi_get_page_type_name( $post_id = 0 ) {
	$post_id = papi_get_post_id( $post_id );

	if ( empty( $post_id ) ) {
		return '';
	}

	$page_type_id = papi_get_page_type_id( $post_id );

	if ( empty( $page_type_id ) ) {
		return '';
	}

	$page_type = papi_get_page_type_by_id( $page_type_id );

	if ( empty( $page_type ) ) {
		return '';
	}

	return $page_type->name;
}

/**
 * Get template file from post id.
 *
 * @param  int|string $post_id
 *
 * @return null|string
 */
function papi_get_page_type_template( $post_id = 0 ) {
	if ( empty( $post_id ) && ! is_numeric( $post_id ) ) {
		return;
	}

	$data = papi_get_page_type_by_post_id( $post_id );

	if ( isset( $data ) && isset( $data->template ) ) {
		$template  = $data->template;
		$extension = '.php';
		$ext_reg   = '/(' . $extension . ')+$/';

		if ( preg_match( '/\.\w+$/', $template, $matches ) && preg_match( $ext_reg, $matches[0] ) ) {
			return str_replace( '.', '/', preg_replace( '/' . $matches[0] . '$/', '', $template ) ) . $matches[0];
		} else {
			$template = str_replace( '.', '/', $template );
			return substr( $template, -strlen( $extension ) ) === $extension
				? $template : $template . $extension;
		}
	}
}

/**
 * Get all post types Papi should work with.
 *
 * @return array
 */
function papi_get_post_types() {
	$page_types = papi_get_all_page_types( true );
	$post_types = [];

	foreach ( $page_types as $page_type ) {
		$post_types = array_merge(
			$post_types,
			papi_to_array( $page_type->post_type )
		);
	}

	return array_unique( $post_types );
}

/**
 * Get boxes with properties slug for a page.
 *
 * @param  int $post_id
 *
 * @return array
 */
function papi_get_slugs( $post_id = 0 ) {
	$page = papi_get_page( $post_id );

	if ( $page instanceof Papi_Post_Page === false ) {
		return [];
	}

	$page_type = $page->get_page_type();

	if ( empty( $page_type ) ) {
		return [];
	}

	$value = [];
	$boxes = $page_type->get_boxes();

	foreach ( $boxes as $box ) {
		if ( count( $box ) < 2 || empty( $box[0]['title'] ) || ! is_array( $box[1] ) ) {
			continue;
		}

		if ( ! isset( $value[$box[0]['title']] ) ) {
			$value[$box[0]['title']] = [];
		}

		foreach ( $box[1] as $property ) {
			$value[$box[0]['title']][] = $property->get_slug( true );
		}
	}

	return $value;
}

/**
 * Check if `$obj` is a instanceof `Papi_Option_Type`.
 *
 * @param  mixed $obj
 *
 * @return bool
 */
function papi_is_option_type( $obj ) {
	return $obj instanceof Papi_Option_Type && $obj->get_post_type() === '_papi_option_type';
}

/**
 * Check if `$obj` is a instanceof `Papi_Page_Type`.
 *
 * @param  mixed $obj
 *
 * @return bool
 */
function papi_is_page_type( $obj ) {
	return $obj instanceof Papi_Page_Type && ! papi_is_option_type( $obj );
}

/**
 * Check if page type exists.
 *
 * @param  string $id
 *
 * @return bool
 */
function papi_page_type_exists( $id ) {
	$exists     = false;
	$page_types = papi_get_all_page_types( true );

	foreach ( $page_types as $page_type ) {
		if ( $page_type->match_id( $id ) ) {
			$exists = true;
			break;
		}
	}

	return $exists;
}

/**
 * Set page type to a post.
 *
 * @param  mixed $post_id
 * @param  string $page_type
 *
 * @return bool
 */
function papi_set_page_type_id( $post_id, $page_type ) {
	return papi_page_type_exists( $page_type ) && update_post_meta(
		papi_get_post_id( $post_id ),
		papi_get_page_type_key(),
		$page_type
	);
}

/**
 * Echo the Page type name.
 *
 * @param  int $post_id
 *
 * @return string
 */
function the_papi_page_type_name( $post_id = 0 ) {
	echo papi_get_page_type_name( $post_id );
}
