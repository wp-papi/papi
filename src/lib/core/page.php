<?php

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

	$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE `meta_key` = '%s' AND `meta_value` = '%s'";
	$sql = $wpdb->prepare( $sql, papi_get_page_type_key(), $page_type );

	return intval( $wpdb->get_var( $sql ) );
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
		$title = $box->title;

		if ( ! isset( $value[$title] ) ) {
			$value[$title] = [];
		}

		foreach ( $box->properties as $property ) {
			$value[$title][] = $property->get_slug( true );
		}
	}

	return $value;
}
