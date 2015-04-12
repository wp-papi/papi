<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering actions functions.
 *
 * @package Papi
 */

class Papi_Lib_Actions_Test extends WP_UnitTestCase {

	/**
	 * Test `papi_action_include` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_action_include() {
		papi_action_include();
		$this->assertNotFalse( did_action( 'papi/include' ) );
	}

	/**
	 * Test `papi_action_include_properties` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_action_include_properties() {
		papi_action_include_properties();
		$this->assertNotFalse( did_action( 'papi_include_properties' ) );
	}

}
