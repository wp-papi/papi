<?php

/**
 * This file is empty for now.
 * It will contain deprecated functions.
 */

/**
 * Get the data page.
 *
 * @param  int    $id
 * @param  string $type
 *
 * @return Papi_Core_Meta_Store
 */
function papi_get_page( $id = 0, $type = 'post' ) {
	_deprecated_function( __FUNCTION__, '3.2.0' );

	return papi_get_meta_store( $id, $type );
}
