<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property hidden.
 *
 * @package Papi
 */

class Papi_Property_Html_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( [
			'type'     => 'html',
			'title'    => 'The html field',
			'settings' => [
				'html' => '<p>Hello, world!</p>'
			]
		] );

		$this->property2 = papi_property( [
			'type'     => 'html',
			'title'    => 'The html field',
			'settings' => [
				'html' => [$this, 'output_html']
			]
		] );
	}

	public function output_html() {
		?>
		<p>Hello, callable!</p>
		<?php
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id, $this->property );
	}

	/**
	 * Test output to check if property slug exists and the property type value.
	 *
	 * @since 1.3.0
	 */

	public function test_output() {
		papi_render_property( $this->property );
		$this->expectOutputRegex( '/data\-property=\"' . $this->property->type . '\"/' );
		$this->expectOutputRegex( '/\<p\>Hello, world!\<\/p\>/' );

		papi_render_property( $this->property2 );
		$this->expectOutputRegex( '/data\-property=\"' . $this->property2->type . '\"/' );
		$this->expectOutputRegex( '/\<p\>Hello, callable!\<\/p\>/' );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.3.0
	 */

	public function test_property_options() {
		$this->assertEquals( 'html', $this->property->type );
		$this->assertEquals( 'The html field', $this->property->title );
	}

}
