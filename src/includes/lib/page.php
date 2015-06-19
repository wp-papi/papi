<?php

/**
 * Papi page functions.
 *
 * @package Papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check if the page type should be displayed or not.
 *
 * @param string|object $page_type
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

	// Run show page type filter.
	return papi_filter_settings_show_page_type( $post_type, $page_type );
}

/**
 * Get all page types that exists.
 *
 * @param bool $all
 * @param string $post_type
 * @param bool $fake_post_types
 *
 * @return array
 */

function papi_get_all_page_types( $all = false, $post_type = null, $fake_post_types = false ) {
	if ( is_null( $post_type ) || empty( $post_type ) ) {
		$post_type  = papi_get_post_type();
	}

	$cache_key   = papi_get_cache_key( sprintf( '%s_%s', $all, $post_type ), $fake_post_types );
	$page_types  = wp_cache_get( $cache_key );

	if ( empty( $page_types ) ) {
		$files = papi_get_all_page_type_files();

		foreach ( $files as $file ) {
			$page_type = papi_get_page_type( $file );

			if ( is_null( $page_type ) ) {
				continue;
			}

			if ( ! is_subclass_of( $page_type, 'Papi_Page_Type' ) ) {
				continue;
			}

			if ( $fake_post_types ) {
				if ( isset( $page_type->post_type[0] ) && ! post_type_exists( $page_type->post_type[0] ) ) {
					$page_types[] = $page_type;
				}
				continue;
			}

			// Add the page type if the post types is allowed.
			if ( ! is_null( $page_type ) && papi_current_user_is_allowed( $page_type->capabilities ) && ( $all || in_array( $post_type, $page_type->post_type ) ) ) {
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
 * Get the data page.
 *
 * @param int $post_id
 * @param string $type
 *
 * @return mixed
 */

function papi_get_page( $post_id = 0, $type = 'post' ) {
	return Papi_Core_Page::factory( $post_id, $type );
}

/**
 * Get data from page type file.
 *
 * @param int|string $post_id Post id or page type
 *
 * @return Papi_Page_Type
 */

function papi_get_file_data( $post_id ) {
	$post_id   = papi_get_post_id( $post_id );
	$page_type = papi_get_page_type_id( $post_id );

	// Check so the page type isn't null or empty before we
	// trying to get the page type meta data.
	if ( ! empty( $page_type ) ) {
		return papi_get_page_type_by_id( $page_type );
	}
}

/**
 * Get number of how many pages uses the given page type.
 * This will also work with only page type.
 *
 * @param string|object $page_type
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

	$cache_key = papi_get_cache_key( 'page_type', $page_type );
	$value = wp_cache_get( $cache_key );

	if ( $value === false ) {

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE `meta_key` = '%s' AND `meta_value` = '%s'";
		$sql = $wpdb->prepare( $sql, PAPI_PAGE_TYPE_KEY, $page_type );

		$value = intval( $wpdb->get_var( $sql ) );

		wp_cache_set( $cache_key, $value );
	}

	return $value;
}

/**
 * Get template file from post id.
 *
 * @param int|string $post_id
 *
 * @return null|string
 */

function papi_get_page_type_template( $post_id ) {
	$data = papi_get_file_data( $post_id );

	if ( isset( $data ) && isset( $data->template ) ) {
		return $data->template;
	}
}

/**
 * Get a page type by file path.
 *
 * @param string $file_path
 *
 * @return Papi_Page_Type
 */

function papi_get_page_type( $file_path ) {
	if ( ! is_file( $file_path ) ) {
		return;
	}

	$class_name = papi_get_class_name( $file_path );

	if ( empty( $class_name ) ) {
		return;
	}

	// Try to add the page type to the container.
	if ( ! papi()->exists( $class_name ) ) {
		if ( ! class_exists( $class_name ) ) {
			require_once $file_path;
		}

		$rc         = new ReflectionClass( $class_name );
		$page_type  = $rc->newInstanceArgs( [$file_path] );

		// If the page type don't have a name we can't use it.
		if ( ! $page_type->has_name() ) {
			return;
		}

		papi()->bind( $class_name, $page_type );
	}

	return papi()->make( $class_name );
}

/**
 * Get page type by identifier.
 *
 * @param string $id
 *
 * @return Papi_Page_Type
 */

function papi_get_page_type_by_id( $id ) {
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
 * Get page type id.
 *
 * @param int $post_id
 *
 * @return string
 */

function papi_get_page_type_id( $post_id = null ) {
	$post_id   = papi_get_post_id( $post_id );
	$key       = PAPI_PAGE_TYPE_KEY;
	$page_type = '';

	if ( $post_id !== 0 ) {
		$meta_value = get_post_meta( $post_id, $key, true );
		$page_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	if ( empty( $page_type ) ) {
		$page_type = str_replace( 'papi/', '', papi_get_qs( 'page_type' ) );
	}

	if ( empty( $page_type ) ) {
		$page_type = papi_get_sanitized_post( PAPI_PAGE_TYPE_KEY );
	}

	// Load right page type from a post query string
	if ( empty( $page_type ) ) {
		$from_post = papi_filter_settings_page_type_from_post_qs();

		if ( empty( $from_post ) ) {
			return $page_type;
		}

		$from_post = papi_get_qs( $from_post );

		if ( empty( $from_post ) ) {
			return $page_type;
		}

		$meta_value = get_post_meta( $from_post, $key, true );
		$page_type  = empty( $meta_value ) ? '' : $meta_value;
	}

	return $page_type;
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
		$post_types = array_merge( $post_types, $page_type->post_type );
	}

	if ( empty( $post_types ) ) {
		return ['page'];
	}

	return array_unique( $post_types );
}

/**
 * Get the Page type name.
 *
 * Example:
 *
 * `papi_get_page_type_name()` will return page type name.
 *
 * @param int $post_id
 *
 * @return string
 */

function papi_get_page_type_name( $post_id = null ) {
	$post_id = papi_get_post_id( $post_id );

	if ( empty( $post_id ) ) {
		return '';
	}

	$page_type_id = get_post_meta( $post_id, PAPI_PAGE_TYPE_KEY, true );

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
 * Echo the Page type name.
 *
 * Example:
 *
 * `the_papi_page_type_name()` will echo the page type name.
 *
 * @param int $post_id
 *
 * @return string
 */

function the_papi_page_type_name( $post_id = null ) {
	echo papi_get_page_type_name( $post_id );
}
