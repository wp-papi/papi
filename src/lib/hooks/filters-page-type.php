<?php

/**
 * Get the only page type that will be used for the given post type.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_only_page_type( $post_type ) {
	$page_type = apply_filters( 'papi/settings/only_page_type_' . $post_type, '' );

	if ( ! is_string( $page_type ) ) {
		return '';
	}

	return str_replace( '.php', '', $page_type );
}

/**
 * Show page type in add new page view for the given post type.
 *
 * @param string        $post_type
 * @param string|object $page_type
 *
 * @return bool
 */
function papi_filter_settings_show_page_type( $post_type, $page_type ) {
	if ( is_object( $page_type ) && method_exists( $page_type, 'get_id' ) ) {
		$page_type = $page_type->get_id();
	}

	$value = apply_filters( 'papi/settings/show_page_type_' . $post_type, $page_type );

	if ( $value === $page_type ) {
		return true;
	}

	if ( ! is_bool( $value ) ) {
		return false;
	}

	return $value;
}

/**
 * Get standard page description for the given post type.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_standard_page_type_description( $post_type ) {
	$name = papi_get_post_type_label( $post_type, 'singular_name', 'Page' );

	// New filter, with `type` in the filter tag.
	$tag = 'papi/settings/standard_page_type_description_' . $post_type;
	$out = apply_filters( $tag, sprintf( __( '%s with WordPress standard fields', 'papi' ), $name ) );

	// Old filter, that didn't have `type` in the filter tag.
	// Should work until Papi 4.0.0.
	$tag = 'papi/settings/standard_page_description_' . $post_type;
	$out = apply_filters( $tag, $out );

	return $out;
}

/**
 * Get standard page name for the given post type.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_standard_page_type_name( $post_type ) {
	$name = papi_get_post_type_label( $post_type, 'singular_name', 'Page' );

	// New filter, with `type` in the filter tag.
	$tag = 'papi/settings/standard_page_type_name_' . $post_type;
	$out = apply_filters( $tag, sprintf( __( 'Standard %s', 'papi' ), $name ) );

	// Old filter, that didn't have `type` in the filter tag.
	// Should work until Papi 4.0.0.
	$tag = 'papi/settings/standard_page_name_' . $post_type;
	$out = apply_filters( $tag, $out );

	return $out;
}

/**
 * Show standard page type on the given post type.
 *
 * @param  string $post_type
 *
 * @return bool
 */
function papi_filter_settings_show_standard_page_type( $post_type ) {
	return ! apply_filters( 'papi/settings/show_standard_page_type_' . $post_type, false ) === false;
}

/**
 * Show standard page type in filter dropdown on the given post type.
 *
 * @param  string $post_type
 *
 * @return bool
 */
function papi_filter_settings_show_standard_page_type_in_filter( $post_type ) {
	$tag = 'papi/settings/show_standard_page_type_in_filter_' . $post_type;

	return ! apply_filters( $tag, papi_filter_settings_show_standard_page_type( $post_type ) ) === false;
}

/**
 * Get standard page thumbnail for the given post type.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_standard_page_type_thumbnail( $post_type ) {
	// New filter, with `type` in the filter tag.
	$tag = 'papi/settings/standard_page_type_thumbnail_' . $post_type;
	$out = apply_filters( $tag, '' );

	// Old filter, that didn't have `type` in the filter tag.
	// Should work until Papi 4.0.0.
	$tag = 'papi/settings/standard_page_thumbnail_' . $post_type;
	$out = apply_filters( $tag, $out );

	return $out;
}
