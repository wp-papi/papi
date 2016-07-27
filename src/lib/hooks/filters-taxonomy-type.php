<?php

/**
 * Get the only taxonomy type that will be used for the given taxonomy.
 *
 * @param  string $taxonomy
 *
 * @return string
 */
function papi_filter_settings_only_taxonomy_type( $taxonomy ) {
	$taxonomy_type = apply_filters( 'papi/settings/only_taxonomy_type_' . $taxonomy, '' );

	if ( ! is_string( $taxonomy_type ) ) {
		return '';
	}

	return str_replace( '.php', '', $taxonomy_type );
}

/**
 * Get standard taxonomy name for the given taxonomy.
 *
 * @param  string $taxonomy
 *
 * @return string
 */
function papi_filter_settings_standard_taxonomy_type_name( $taxonomy ) {
	$name = papi_get_taxonomy_label( $taxonomy, 'singular_name', 'Taxonomy' );
	$tag = 'papi/settings/standard_taxonomy_name_' . $taxonomy;

	return apply_filters( $tag, sprintf( __( 'Standard %s', 'papi' ), $name ) );
}

/**
 * Show standard taxonomy type on the given taxonomy.
 *
 * @param  string $taxonomy
 *
 * @return bool
 */
function papi_filter_settings_show_standard_taxonomy_type( $taxonomy ) {
	return ! apply_filters( 'papi/settings/show_standard_taxonomy_type_' . $taxonomy, false ) === false;
}
