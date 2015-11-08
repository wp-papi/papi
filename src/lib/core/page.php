<?php

/**
 * Get the data page.
 *
 * @param  int    $post_id
 * @param  string $type
 *
 * @return Papi_Core_Page|null
 */
function papi_get_page( $post_id = 0, $type = 'page' ) {
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
