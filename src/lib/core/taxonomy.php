<?php

/**
 * Get term id from a object.
 *
 * @param  mixed $term_id
 *
 * @return int
 */
function papi_get_term_id( $term_id = null ) {
	if ( is_object( $term_id ) && isset( $term_id->term_id ) ) {
		return $term_id->term_id;
	}

	if ( is_numeric( $term_id ) && is_string( $term_id ) && $term_id !== '0' ) {
		return intval( $term_id );
	}

	if ( is_null( $term_id ) || intval( $term_id ) === 0 ) {
		if ( ! papi_is_admin() && ( is_category() || is_tag() || is_tax() ) ) {
			return get_queried_object_id();
		} else if ( $term_id = papi_get_or_post( 'term_id' ) ) {
			return intval( $term_id );
		} else if ( $tag_id = papi_get_or_post( 'tag_ID' ) ) {
			return intval( $tag_id );
		}
	}

	return intval( $term_id );
}

/**
 * Get WordPress taxonomy in various ways.
 *
 * @param  int $term_id
 *
 * @return string
 */
function papi_get_taxonomy( $term_id = null ) {
	if ( $taxonomy = papi_get_or_post( 'taxonomy' ) ) {
		return $taxonomy;
	}

	$term_id = papi_get_term_id( $term_id );

	if ( ! empty( $term_id ) ) {
		$term = get_term( $term_id, '' );

		if ( is_object( $term ) && ! is_wp_error( $term ) ) {
			return strtolower( $term->taxonomy );
		}
	}

	return '';
}

/**
 * Get taxonomy label.
 *
 * @param  string $taxonomy
 * @param  string $label
 * @param  string $default
 *
 * @return string
 */
function papi_get_taxonomy_label( $taxonomy, $label, $default = '' ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return $default;
	}

	return get_taxonomy( $taxonomy )->labels->$label;
}
