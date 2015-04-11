<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering template functions.
 *
 * @package Papi
 */

class Papi_Lib_Template_Test extends WP_UnitTestCase {

	/**
	 * Test `papi_template` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_template() {
		$template = papi_template( papi_test_get_fixtures_path( '/properties/simple.php' ) );

		$this->assertEquals( 'Name', $template['title'] );
		$this->assertEquals( 'string', $template['type'] );

		$this->assertEmpty( papi_template( null ) );
		$this->assertEmpty( papi_template( true ) );
		$this->assertEmpty( papi_template( false ) );
		$this->assertEmpty( papi_template( 1 ) );
		$this->assertEmpty( papi_template( array() ) );
		$this->assertEmpty( papi_template( new stdClass() ) );

		$template = papi_template( papi_test_get_fixtures_path( '/properties/array.php' ), array(), true );

		$this->assertEquals( 'Name', $template->title );
		$this->assertEquals( 'string', $template->type );

		$this->assertEmpty( papi_template( 'hello' )  );
	}

}
