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
		$page_type = papi_get_entry_type_by_id( $page_type );
	}

	if ( ! is_object( $page_type ) ) {
		return false;
	}

	if ( ! in_array( $post_type, $page_type->post_type, true ) ) {
		return false;
	}

	$display = $page_type->display( $post_type );

	if ( ! is_bool( $display ) || $display === false ) {
		return false;
	}

	if ( preg_match( '/papi\-standard\-\w+\-type/', $page_type->get_id() ) ) {
		return true;
	}

	$parent_page_type = papi_get_entry_type_by_meta_id( papi_get_parent_post_id() );

	if ( $parent_page_type instanceof Papi_Page_Type ) {
		$child_types = $parent_page_type->get_child_types();

		if ( ! empty( $child_types ) ) {
			return in_array( $page_type, $parent_page_type->get_child_types(), true );
		}
	}

	// Run show page type filter.
	return papi_filter_settings_show_page_type( $post_type, $page_type );
}

/**
 * Get all page types based on a post type.
 *
 * @param  string $post_type
 *
 * @return array
 */
function papi_get_all_page_types( $post_type = '' ) {
	$page_types = papi_get_all_entry_types( [
		'args'  => $post_type,
		'mode'  => 'include',
		'types' => ['attachment', 'page']
	] );

	if ( is_array( $page_types ) ) {
		usort( $page_types, function ( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );
	}

	return papi_sort_order( array_reverse( $page_types ) );
}

/**
 * Get page type id by post id.
 *
 * @param  int $post_id
 *
 * @return string
 */
function papi_get_page_type_id( $post_id = 0 ) {
	return papi_get_entry_type_id( $post_id, 'post' );
}

/**
 * Get the page type key that is used for each post.
 *
 * @param  string $suffix
 *
 * @return string
 */
function papi_get_page_type_key( $suffix = '' ) {
	$key    = defined( 'PAPI_PAGE_TYPE_KEY' ) ? PAPI_PAGE_TYPE_KEY : '_papi_page_type';
	$suffix = ltrim( $suffix, '_' );

	return empty( $suffix ) ? $key : $key . '_' . $suffix;
}

/**
 * Get the page type name.
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

	$entry_type_id = papi_get_page_type_id( $post_id );

	if ( empty( $entry_type_id ) ) {
		return '';
	}

	$entry_type = papi_get_entry_type_by_id( $entry_type_id );

	if ( empty( $entry_type ) ) {
		return '';
	}

	return $entry_type->name;
}

/**
 * Get all post types Papi should work with.
 *
 * @return array
 */
function papi_get_post_types() {
	$post_types = [];
	$page_types = papi_get_all_entry_types( [
		'types' => ['attachment', 'page']
	] );

	foreach ( $page_types as $page_type ) {
		$post_types = array_merge( $post_types, papi_to_array( $page_type->post_type ) );
	}

	return array_unique( $post_types );
}

/**
 * Get standard page type since it's not a real page type class.
 *
 * @return null|Papi_Page_Type
 */
function papi_get_standard_page_type( $post_type ) {
	if ( ! is_string( $post_type ) ) {
		return;
	}

	// Create a new page type and set required fields.
	$standard_type              = new Papi_Page_Type();
	$standard_type->name        = papi_filter_settings_standard_page_type_name( $post_type );
	$standard_type->description = papi_filter_settings_standard_page_type_description( $post_type );
	$standard_type->thumbnail   = papi_filter_settings_standard_page_type_thumbnail( $post_type );
	$standard_type->post_type   = [$post_type];

	// Set standard page type id.
	$standard_type->set_id( papi_get_standard_page_type_id( $post_type ) );

	return $standard_type;
}

/**
 * Get standard page type id.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_get_standard_page_type_id( $post_type ) {
	$post_type = is_string( $post_type ) ? $post_type : '';
	return sprintf( 'papi-standard-%s-type', $post_type );
}

/**
 * Check if given string is a page type.
 *
 * @param  string $str
 *
 * @return bool
 */
function papi_is_page_type( $str = '' ) {
	return papi_is_entry_type( $str );
}

/**
 * Load the entry type id on a post types.
 *
 * @param  string $entry_type_id
 * @param  string $type
 * @param  int $post_id
 *
 * @return string
 */
function papi_load_page_type_id( $entry_type_id = '', $type = 'post', $post_id = null ) {
	$type = papi_get_meta_type( $type );

	if ( $type !== 'post' ) {
		return $entry_type_id;
	}

	$post_id   = papi_get_post_id( $post_id );
	$key       = papi_get_page_type_key();
	$post_type = papi_get_post_type( $post_id );

	// Try to load the entry type id from only page type filter.
	if ( empty( $entry_type_id ) ) {
		$entry_type_id = papi_filter_settings_only_page_type( $post_type );
	}

	// If we have a post id we can load the entry type id from the post.
	if ( empty( $entry_type_id ) && $post_id > 0 ) {
		$meta_value    = get_post_meta( $post_id, $key, true );
		$entry_type_id = empty( $meta_value ) ? '' : $meta_value;
	}

	// Try to fetch the entry type id from `page_type` query string.
	if ( empty( $entry_type_id ) ) {
		$entry_type_id = papi_get_qs( 'page_type' );
	}

	// Load right entry type from the parent post id.
	if ( empty( $entry_type_id ) ) {
		$meta_value = get_post_meta( papi_get_parent_post_id(), $key, true );
		$entry_type_id = empty( $meta_value ) ? '' : $meta_value;
	}

	// Try to load the entry type from all page types and check
	// if only one exists of that post type.
	//
	// The same as only page type filter but without the filter.
	if ( empty( $entry_type_id ) ) {
		$key = sprintf( 'entry_type_id.post_type.%s', $post_type );

		if ( papi()->exists( $key ) ) {
			return papi()->make( $key );
		}

		$entries = papi_get_all_page_types( $post_type );

		if ( count( $entries ) === 1 ) {
			$entry_type_id = $entries[0]->get_id();

			papi()->bind( $key, $entry_type_id );
		}
	}

	// If standard page is enabled and entry type id is empty it's a standard type.
	if ( empty( $entry_type_id ) && papi_filter_settings_show_standard_page_type( $post_type ) ) {
		return papi_get_standard_page_type_id( $post_type );
	}

	return $entry_type_id;
}

add_filter( 'papi/entry_type_id', 'papi_load_page_type_id', 10, 3 );

/**
 * Set page type to a post.
 *
 * @param  mixed  $post_id
 * @param  string $page_type
 *
 * @return bool
 */
function papi_set_page_type_id( $post_id, $page_type ) {
	if ( papi_entry_type_exists( $page_type ) ) {
		return update_post_meta( papi_get_post_id( $post_id ), papi_get_page_type_key(), $page_type );
	}

	return false;
}

/**
 * Echo the page type name.
 *
 * @param  int $post_id
 *
 * @return string
 */
function the_papi_page_type_name( $post_id = 0 ) {
	echo esc_html( papi_get_page_type_name( $post_id ) );
}
