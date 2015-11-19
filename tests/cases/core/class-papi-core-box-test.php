<?php

/**
 * @group core
 */
class Papi_Core_Box_Test extends WP_UnitTestCase {

	public function test_get_option_null() {
		$box = new Papi_Core_Box;
		$this->assertNull( $box->get_option( 'empty' ) );
	}

	public function test_option_value() {
		$box = new Papi_Core_Box;
		$box->set_option( 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $box->get_option( 'name' ) );
	}

	public function test_options() {
		$box = new Papi_Core_Box( [
			'title' => 'Box'
		] );
		$this->assertSame( 'Box', $box->title );
		$this->assertSame( '_papi_box', $box->id );
	}

	public function test_excluded_options() {
		$box = new Papi_Core_Box( [
			'title'      => 'Box',
			'options'    => null,
			'properties' => null,
		] );
		$this->assertSame( 'Box', $box->title );
		$this->assertSame( '_papi_box', $box->id );
		$this->assertSame( [], $box->properties );
	}
}
