<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering property functionality.
 *
 * @package Papi
 */

class WP_Papi_Property_Divider extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'  => 'divider',
			'title' => 'Divider'
		) );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options() {
		$this->assertEquals( 'divider', $this->property->type );
		$this->assertEquals( 'Divider', $this->property->title );
	}

}
