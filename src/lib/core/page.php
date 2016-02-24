<?php

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
