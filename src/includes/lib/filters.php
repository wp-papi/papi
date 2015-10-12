<?php

/**
 * Get conditional rule value.
 *
 * @param  array|Papi_Core_Conditional_Rule $rule
 *
 * @return bool
 */
function papi_filter_conditional_rule_allowed( $rule ) {
	$rule = papi_rule( $rule );

	if ( ! papi_is_rule( $rule ) ) {
		return false;
	}

	$result = apply_filters(
		'papi/conditional/rule/' . $rule->operator,
		$rule
	);

	if ( $result === true || $result === false ) {
		return $result;
	}

	return false;
}

/**
 * This filter will return all post types that should load only one page type.
 *
 * @return array
 */
function papi_filter_core_load_one_type_on() {
	$default = ['attachment'];
	$result  = apply_filters(
		'papi/core/load_one_type_on',
		$default
	);

	return is_array( $result ) ? $result : $default;
}

/**
 * Format the value of the property before it's returned to WordPress admin or the site.
 *
 * @param  string $type
 * @param  mixed  $value
 * @param  string $slug
 * @param  int    $post_id
 *
 * @return mixed
 */
function papi_filter_format_value( $type, $value, $slug, $post_id ) {
	return apply_filters(
		'papi/format_value/' . $type,
		$value,
		$slug,
		$post_id
	);
}

/**
 * This filter is applied after the value is loaded in the database.
 *
 * @param  string $type
 * @param  mixed  $value
 * @param  string $slug
 * @param  int    $post_id
 *
 * @return mixed
 */
function papi_filter_load_value( $type, $value, $slug, $post_id ) {
	return apply_filters(
		'papi/load_value/' . $type,
		$value,
		$slug,
		$post_id
	);
}

/**
 * Get all registered page type directories.
 *
 * @return array
 */
function papi_filter_settings_directories() {
	$directories = apply_filters(
		'papi/settings/directories',
		[]
	);

	if ( is_string( $directories ) ) {
		return [$directories];
	}

	if ( ! is_array( $directories ) ) {
		return [];
	}

	return array_filter( $directories, function ( $directory ) {
		return is_string( $directory );
	} );
}

/**
 * Get the only page type that will be used for the given post type.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_only_page_type( $post_type ) {
	$page_type = apply_filters(
		'papi/settings/only_page_type_' . $post_type,
		''
	);

	if ( ! is_string( $page_type ) ) {
		return '';
	}

	return str_replace( '.php', '', $page_type );
}

/**
 * Change column title for page type column.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_page_type_column_title( $post_type ) {
	return apply_filters(
		'papi/settings/column_title_' . $post_type,
		__( 'Type', 'papi' )
	);
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

	$value = apply_filters(
		'papi/settings/show_page_type_' . $post_type,
		$page_type
	);

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
function papi_filter_settings_standard_page_description( $post_type ) {
	$name = papi_get_post_type_label( $post_type, 'singular_name', 'Page' );

	return apply_filters(
		'papi/settings/standard_page_description_' . $post_type,
		sprintf( __( '%s with WordPress standard fields', 'papi' ), $name )
	);
}

/**
 * Get standard page name for the given post type.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_standard_page_name( $post_type ) {
	$name = papi_get_post_type_label( $post_type, 'singular_name', 'Page' );

	return apply_filters(
		'papi/settings/standard_page_name_' . $post_type,
		sprintf( __( 'Standard %s', 'papi' ), $name )
	);
}

/**
 * Show standard page type on the given post type.
 *
 * @param  string $post_type
 *
 * @return bool
 */
function papi_filter_settings_show_standard_page_type( $post_type ) {
	return ! apply_filters(
		'papi/settings/show_standard_page_type_' . $post_type,
		false
	) === false;
}

/**
 * Show standard page type in filter dropdown on the given post type.
 *
 * @param  string $post_type
 *
 * @return bool
 */
function papi_filter_settings_show_standard_page_type_in_filter( $post_type ) {
	return ! apply_filters(
		'papi/settings/show_standard_page_type_in_filter_' . $post_type,
		papi_filter_settings_show_standard_page_type( $post_type )
	) === false;
}

/**
 * Get standard page thumbnail for the given post type.
 *
 * @param  string $post_type
 *
 * @return string
 */
function papi_filter_settings_standard_page_thumbnail( $post_type ) {
	return apply_filters(
		'papi/settings/standard_page_thumbnail_' . $post_type,
		''
	);
}

/**
 * Get the default sort order that is 1000.
 *
 * @return int
 */
function papi_filter_settings_sort_order() {
	return intval( apply_filters( 'papi/settings/sort_order', 1000 ) );
}

/**
 * This filter is applied before the value is saved in the database.
 *
 * @param  string $type
 * @param  mixed  $value
 * @param  string $slug
 * @param  int    $post_id
 *
 * @return mixed
 */
function papi_filter_update_value( $type, $value, $slug, $post_id ) {
	return apply_filters(
		'papi/update_value/' . $type,
		$value,
		$slug,
		$post_id
	);
}
