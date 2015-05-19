<?php

/**
 * Papi actions functions.
 *
 * @package Papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Fire the 'papi/include' action, where plugins should include files.
 */

function papi_action_include() {
	do_action( 'papi/include' );
}
