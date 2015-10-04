<?php

/**
 * Unit tests covering property link.
 *
 * @package Papi
 */
class Papi_Property_Link_Test extends Papi_Property_Test_Case {

	public $slug = 'link_test';

	public function get_value() {
		return [
			'url'    => 'http://example.org',
			'title'  => 'Example site',
			'target' => '_blank'
		];
	}

	public function get_expected() {
		return (object) [
			'url'    => 'http://example.org',
			'title'  => 'Example site',
			'target' => '_blank'
		];
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'object', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_delete_value() {
		$this->save_properties_value( $this->property );
		$result = $this->property->delete_value( $this->property->slug, $this->post_id, 'post' );
		$this->assertTrue( $result );
	}

	public function test_property_import_value() {
		$expected = [
			'papi_link_test_url'    => 'http://example.org',
			'papi_link_test_title'  => 'Example site',
			'papi_link_test_target' => '_blank',
			'papi_link_test'        => 1
		];
		$this->assertEquals( $expected, $this->property->import_value( $this->get_value(), $this->property->slug, 0 ) );
		$this->assertEquals( $expected, $this->property->import_value( (object) $this->get_value(), $this->property->slug, 0 ) );

		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
		$this->assertNull( $this->property->import_value( 1, '', 0 ) );
		$this->assertNull( $this->property->import_value( 'test', '', 0 ) );
	}

	public function test_property_load_value() {
		$post_id = $this->factory->post->create();
		$value   = [
			'_papi_link_target' => '_blank',
			'_papi_link_title'  => 'Example site',
			'_papi_link_url'    => 'http://example.org'
		];

		$output = $this->property->load_value( $value, '_papi_link', $post_id );

		$this->assertEquals( '_blank', $output->target );
		$this->assertEquals( 'Example site', $output->title );
		$this->assertEquals( 'http://example.org', $output->url );

		$output = $this->property->load_value( (object) $value, '_papi_link', $post_id );

		$this->assertEquals( '_blank', $output->target );
		$this->assertEquals( 'Example site', $output->title );
		$this->assertEquals( 'http://example.org', $output->url );

		foreach ( $value as $k => $v ) {
			update_post_meta( $post_id, $k, $v );
		}

		$output = $this->property->load_value( null, '_papi_link', $post_id );

		$this->assertEquals( '_blank', $output->target );
		$this->assertEquals( 'Example site', $output->title );
		$this->assertEquals( 'http://example.org', $output->url );

		$output = $this->property->load_value( [ 'test' => ''], 'test', $post_id );
		$this->assertEmpty( (array) $output );
	}

	public function test_property_options() {
		$this->assertEquals( 'link', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Link test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_link_test', $this->property->get_option( 'slug' ) );
	}

	public function test_render_link_template() {
		$this->property->render_link_template();
		$this->expectOutputRegex( '/.*\S.*/' );
	}
}
