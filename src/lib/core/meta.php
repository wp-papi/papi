<?php

/**
 * Get right meta type value.
 *
 * @param  string $type
 *
 * @return string
 */
function papi_get_meta_type( $type = 'post' ) {
	switch ( $type ) {
		case 'post':
		case 'page':
			return 'post';
		case 'taxonomy':
		case 'term':
			return 'term';
		default:
			return $type;
	}
}

function papi_get_meta_id( $type = 'post' ) {
	$type = papi_get_meta_type( $type );
	return sprintf( '%s_id', $type );
}
