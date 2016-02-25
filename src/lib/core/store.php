<?php

/**
 * Get the data store.
 *
 * @param  int    $post_id
 * @param  string $type
 *
 * @return Papi_Core_Store|null
 */
function papi_get_store( $post_id = 0, $type = 'post' ) {
	return Papi_Core_Store::factory( $post_id, $type );
}
