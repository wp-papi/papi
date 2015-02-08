<?php

/**
 * Papi page functions.
 *
 * @package Papi
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the current page. Like in EPiServer.
 *
 * @since 1.0.0
 *
 * @return Papi_Page|null
 */

function current_page() {
	return papi_get_page();
}

/**
 * Get all page types that exists.
 *
 * @since 1.0.0
 *
 * @param bool $all Default false
 * @param string $post_type Default null (since 1.1.0)
 *
 * @return array
 */

function papi_get_all_page_types( $all = false, $post_type = null ) {
	$files      = papi_get_all_page_type_files();
	$page_types = array();

	if ( is_null( $post_type ) || empty( $post_type ) ) {
		$post_type  = papi_get_wp_post_type();
	}

	foreach ( $files as $file ) {
		$page_type = papi_get_page_type( $file );

		if ( is_null( $page_type ) ) {
			continue;
		}

		// Add the page type if the post types is allowed.
		if ( ! is_null( $page_type ) && papi_current_user_is_allowed( $page_type->capabilities ) && ( $all || in_array( $post_type, $page_type->post_type ) ) ) {
			$page_types[] = $page_type;
		}
	}

	usort( $page_types, function ( $a, $b ) {
		return strcmp( $a->name, $b->name );
	} );

	$page_types = papi_sort_order( array_reverse( $page_types ) );

	return $page_types;
}

/**
 * Get data from page type file.
 *
 * @param int|string $post_id Post id or page type
 *
 * @since 1.0.0
 *
 * @return null|Papi_Page_Type
 */

function papi_get_file_data( $post_id ) {
	$post_id   = papi_get_post_id( $post_id );
	$page_type = papi_get_page_type_meta_value( $post_id );

	// Check so the page type isn't null or empty before we
	// trying to get the page type meta data.
	if ( ! empty( $page_type ) ) {
		return papi_get_page_type( papi_get_file_path( $page_type ) );
	}

	return null;
}

/**
 * Get number of how many pages uses the given page type.
 * This will also work with only page type.
 *
 * @param string|object $page_type
 *
 * @since 1.0.0
 *
 * @return int
 */

function papi_get_number_of_pages( $page_type ) {
	global $wpdb;

	if ( empty( $page_type ) || ( !is_string( $page_type ) && ( !is_object( $page_type ) ) ) ) {
		return 0;
	}

	if ( is_object( $page_type ) && method_exists( $page_type, 'get_filename' ) ) {
		$file_name = $page_type->get_filename();
		$post_type = '';

		foreach ( $page_type->post_type as $p ) {
			if ( papi_filter_settings_only_page_type( $p ) === $file_name ) {
				$post_type = $p;
				break;
			}
		}

		$query = "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE `post_type` = '$post_type'";
	} else {
		if ( !is_string( $page_type ) ) {
			return 0;
		}

		$query = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE `meta_key` = '" . PAPI_PAGE_TYPE_KEY . "' AND `meta_value` = '$page_type'";
	}

	return intval( $wpdb->get_var( $query ) );
}

/**
 * Get the page.
 *
 * @param int $post_id The post id.
 *
 * @since 1.0.0
 *
 * @return Papi_Page|null
 */

function papi_get_page( $post_id = null ) {
	$post_id = papi_get_post_id( $post_id );
	$page    = new Papi_Page( $post_id );

	if ( ! $page->has_post() ) {
		return null;
	}

	return $page;
}

/**
 * Get template file from post id.
 *
 * @param int|string $post_id
 *
 * @since 1.0.0
 *
 * @return null|string
 */

function papi_get_page_type_template( $post_id ) {
	$data = papi_get_file_data( $post_id );

	if ( isset( $data ) && isset( $data->template ) && isset( $data->template ) ) {
		return $data->template;
	} else {
		return null;
	}
}

/**
 * Get a page type by file path.
 *
 * @param string $file_path
 *
 * @since 1.0.0
 *
 * @return null|Papi_Page_Type
 */

function papi_get_page_type( $file_path ) {
	if ( ! is_file( $file_path ) ) {
		return null;
	}

	$class_name = papi_get_class_name( $file_path );

	if ( empty( $class_name ) ) {
		return null;
	}

	if ( ! class_exists( $class_name ) ) {
		require_once $file_path;
	}

	// Try to get the instance of the page type.
	$instance = call_user_func( $class_name . '::instance' );

	if ( ! empty( $instance ) && get_class( $instance ) === $class_name ) {
		return $instance;
	}

	$rc         = new ReflectionClass( $class_name );
	$page_type  = $rc->newInstanceArgs( array( $file_path ) );

	// Set the instance.
	call_user_func( $class_name . '::instance', $page_type );

	// If the page type don't have a name we can't use it.
	if ( ! $page_type->has_name() ) {
		return null;
	}

	return $page_type;
}

/**
 * Get page type meta value.
 *
 * @param int $post_id
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_get_page_type_meta_value( $post_id = null ) {
	if ( is_null( $post_id ) ) {
		$post_id = papi_get_post_id();
	}

	$key       = PAPI_PAGE_TYPE_KEY;
	$page_type = '';

	if ( ! is_null( $post_id ) ) {
		$meta_value = get_post_meta( $post_id, $key, true );
		$page_type  = papi_h( $meta_value, '' );
	}

	// Get page type value from get object.
	if ( empty( $page_type ) && isset( $_GET['page_type'] ) ) {
		$page_type = $_GET['page_type'];
	}

	// Get page type value from post object.
	if ( empty( $page_type ) && isset( $_POST[PAPI_PAGE_TYPE_KEY] ) ) {
		$page_type = $_POST[PAPI_PAGE_TYPE_KEY];
	}

	// Load right page type from a post query string
	if ( empty( $page_type ) ) {
		$from_post = papi_filter_settings_page_type_from_post_qs();
		if ( ! is_null( $from_post ) && is_numeric( $from_post ) ) {
			$meta_value = get_post_meta( intval( $from_post ), $key, true );
			$page_type  = papi_h( $meta_value, '' );
		}
	}

	return $page_type;
}

/**
 * Get all post types Papi should work with.
 *
 * @since 1.0.0
 *
 * @return array
 */

function papi_get_post_types() {
	$page_types = papi_get_all_page_types( true );
	$post_types = array();

	foreach ( $page_types as $page_type ) {
		$post_types = array_merge( $post_types, $page_type->post_type );
	}

	if ( empty( $post_types ) ) {
		return array( 'page' );
	}

	return array_unique( $post_types );
}

/**
 * Check if page type is allowed to use.
 *
 * @param string $post_type
 *
 * @since 1.0.0
 *
 * @return bool
 */

function papi_is_page_type_allowed( $post_type ) {
	if ( ! is_string( $post_type ) ) {
		return false;
	}

	$post_types = array_map( function ( $p ) {
		return strtolower( $p );
	}, papi_get_post_types() );

	return in_array( strtolower( $post_type ), $post_types );
}
