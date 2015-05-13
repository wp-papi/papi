<?php

/**
 * Papi actions functions.
 *
 * @package Papi
 * @since 1.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Fire the 'papi/include' action, where plugins should include files.
 *
 * @since 1.2.0
 */

function papi_action_include() {
	do_action( 'papi/include' );
}
