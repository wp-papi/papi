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

	$result = apply_filters( 'papi/conditional/rule/' . $rule->operator, $rule );

	if ( $result === true || $result === false ) {
		return $result;
	}

	return false;
}

/**
 * Format the value of the property before it's returned to WordPress admin or the site.
 *
 * @since  3.1.0 `$meta_type` argument was added.
 *
 * @param  string $type
 * @param  mixed  $value
 * @param  string $slug
 * @param  int    $id
 * @param  string $meta_type
 *
 * @return mixed
 */
function papi_filter_format_value( $type, $value, $slug, $id, $meta_type = 'post' ) {
	return apply_filters( 'papi/format_value/' . $type, $value, $slug, $id, $meta_type );
}

/**
 * This filter is applied after the value is loaded in the database.
 *
 * @since  3.1.0 `$meta_type` argument was added.
 *
 * @param  string $type
 * @param  mixed  $value
 * @param  string $slug
 * @param  int    $id
 * @param  string $meta_type
 *
 * @return mixed
 */
function papi_filter_load_value( $type, $value, $slug, $id, $meta_type = 'post' ) {
	return apply_filters( 'papi/load_value/' . $type, $value, $slug, $id, $meta_type );
}

/**
 * Get all registered page type directories.
 *
 * @return array
 */
function papi_filter_settings_directories() {
	$directories = apply_filters( 'papi/settings/directories', [] );

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
 * @since  3.1.0 `$meta_type` argument was added.
 *
 * @param  string $type
 * @param  mixed  $value
 * @param  string $slug
 * @param  int    $id
 * @param  string $meta_type
 *
 * @return mixed
 */
function papi_filter_update_value( $type, $value, $slug, $id, $meta_type = 'post' ) {
	return apply_filters( 'papi/update_value/' . $type, $value, $slug, $id, $meta_type );
}
