<?php

/**
 * Unit tests covering property Url.
 *
 * @package Papi
 */
class Papi_Property_Url_Test extends Papi_Property_Test_Case {

	public $slugs = ['url_test', 'url_mediauploader_test'];

	public function get_value() {
		return 'http://github.com';
	}

	public function get_expected() {
		return 'http://github.com';
	}

	public function test_property_format_value() {
		$this->assertEquals( $this->get_expected( 'url_test' ), $this->properties[0]->format_value( $this->get_value( 'url_test' ), '', 0 ) );
		$this->assertEquals( $this->get_expected( 'url_mediauploader_test' ), $this->properties[1]->format_value( $this->get_value( 'url_mediauploader_test' ), '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertEquals( $this->get_expected( 'url_test' ), $this->properties[0]->import_value( $this->get_value( 'url_test' ), '', 0 ) );
		$this->assertEquals( $this->get_expected( 'url_mediauploader_test' ), $this->properties[1]->import_value( $this->get_value( 'url_mediauploader_test' ), '', 0 ) );
	}

	public function test_property_load_value() {
		$this->assertEquals( 'http://wordpress.org', $this->properties[0]->load_value( 'http://wordpress.org', '', 0 ) );
		$this->assertNull( $this->properties[0]->load_value( 'hello', '', 0 ) );
		$this->assertNull( $this->properties[0]->load_value( null, '', 0 ) );

		$this->assertEquals( 'http://wordpress.org', $this->properties[1]->load_value( 'http://wordpress.org', '', 0 ) );
		$this->assertNull( $this->properties[1]->load_value( 'hello', '', 0 ) );
		$this->assertNull( $this->properties[1]->load_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'url', $this->properties[0]->get_option( 'type' ) );
		$this->assertEquals( 'Url test', $this->properties[0]->get_option( 'title' ) );
		$this->assertEquals( 'papi_url_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertEquals( 'url', $this->properties[1]->get_option( 'type' ) );
		$this->assertEquals( 'Url mediauploader test', $this->properties[1]->get_option( 'title' ) );
		$this->assertEquals( 'papi_url_mediauploader_test', $this->properties[1]->get_option( 'slug' ) );
	}

	public function test_property_output() {
		parent::test_property_output();
		$this->expectOutputRegex( '/class\=\"button papi-url-media-button\"/' );
	}

	public function test_property_update_value() {
		$this->assertEquals( 'http://wordpress.org', $this->properties[0]->update_value( 'http://wordpress.org', '', 0 ) );
		$this->assertNull( $this->properties[0]->update_value( 'hello', '', 0 ) );
		$this->assertNull( $this->properties[0]->update_value( null, '', 0 ) );

		$this->assertEquals( 'http://wordpress.org', $this->properties[1]->update_value( 'http://wordpress.org', '', 0 ) );
		$this->assertNull( $this->properties[1]->update_value( 'hello', '', 0 ) );
		$this->assertNull( $this->properties[1]->update_value( null, '', 0 ) );
	}
}
