<?php

/**
 * Delete data from database.
 *
 * @param  int    $id
 * @param  string $slug
 * @param  string $type
 *
 * @return mixed
 */
function papi_data_delete( $id, $slug, $type = 'post' ) {
	return papi()->data( $type )->delete( $id, $slug, $type );
}

/**
 * Get data from database.
 *
 * @param  int    $id
 * @param  string $slug
 * @param  string $type
 */
function papi_data_get( $id, $slug, $type = 'post' ) {
	return papi()->data( $type )->get( $id, $slug );
}

/**
 * Updata data in database.
 *
 * @param  int    $id
 * @param  string $slug
 * @param  mixed  $value
 * @param  string $type
 *
 * @return bool
 */
function papi_data_update( $id, $slug, $value, $type = 'post' ) {
	return papi()->data( $type )->update( $id, $slug, $value );
}
