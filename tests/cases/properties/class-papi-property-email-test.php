<?php

/**
 * @group properties
 */
class Papi_Property_Email_Test extends Papi_Property_Test_Case {

	public $slug = 'email_test';

	public function get_value() {
		return 'info@github.com';
	}

	public function get_expected() {
		return 'info@github.com';
	}

	public function test_property_options() {
		$this->assertSame( 'email', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Email test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_email_test', $this->property->get_option( 'slug' ) );
	}
}
