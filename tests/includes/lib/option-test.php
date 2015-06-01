<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering options functions.
 *
 * @package Papi
 */

class Papi_Lib_Option_Test extends WP_UnitTestCase {

	public function test_papi_option() {
		$this->assertNull( papi_option( 'site' ) );
	}

}
