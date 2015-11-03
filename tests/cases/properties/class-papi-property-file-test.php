<?php

/**
 * @group properties
 */
class Papi_Property_File_Test extends Papi_Property_Test_Case {

	public $slug = 'file_test';

	public function get_value() {
		return 23;
	}

	public function get_expected() {
		return 23;
	}

	public function test_property_convert_type() {
		$this->assertSame( 'object', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertSame( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertSame( $this->get_expected(), $this->property->format_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_format_value_wrong_values() {
		$this->assertNull( $this->property->format_value( new stdClass, '', 0 ) );
		$this->assertTrue( $this->property->format_value( true, '', 0 ) );
		$this->assertFalse( $this->property->format_value( false, '', 0 ) );
		$this->assertNull( $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_import_value() {
		$post_id  = $this->factory->post->create( ['post_type' => 'attachment'] );
		$post_id2 = $this->factory->post->create( ['post_type' => 'attachment'] );

		$this->assertSame( 0, $this->property->import_value( $this->get_value(), '', 0 ) );
		$this->assertSame( $post_id, $this->property->import_value( (object) ['id' => $post_id], '', 0 ) );
		$this->assertSame( $post_id, $this->property->import_value( $post_id, '', 0 ) );

		$property = $this->page_type->get_property( 'file_test_2' );

		$value = [
			(object) ['id' => $post_id],
			(object) ['id' => $post_id2]
		];
		$this->assertSame( [$post_id, $post_id2], $property->import_value( $value, '', 0 ) );

		$value = [$post_id, $post_id2];
		$this->assertSame( [$post_id, $post_id2], $property->import_value( $value, '', 0 ) );
	}

	public function test_property_import_value_wrong_values() {
		$this->assertEmpty( $this->property->import_value( true, '', 0 ) );
		$this->assertEmpty( $this->property->import_value( false, '', 0 ) );
		$this->assertEmpty( $this->property->import_value( null, '', 0 ) );
		$this->assertEmpty( $this->property->import_value( true, '', 0 ) );
		$this->assertEmpty( $this->property->import_value( new stdClass, '', 0 ) );
		$this->assertEmpty( $this->property->import_value( [true], '', 0 ) );
		$this->assertEmpty( $this->property->import_value( [false], '', 0 ) );
		$this->assertEmpty( $this->property->import_value( [null], '', 0 ) );
		$this->assertEmpty( $this->property->import_value( [new stdClass], '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'file', $this->property->get_option( 'type' ) );
		$this->assertSame( 'File test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_file_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_render_file_template() {
		$this->property->render_file_template();
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_property_settings() {
		$this->assertFalse( $this->property->get_setting( 'multiple' ) );
	}

	public function test_property_wp_get_attachment_metadata() {
		$post_id = $this->factory->post->create( ['post_type' => 'attachment'] );
		$this->assertSame( '', $this->property->wp_get_attachment_metadata( null, $post_id ) );
		$this->assertSame( '', $this->property->wp_get_attachment_metadata( [], $post_id ) );
		$this->assertSame( '', $this->property->wp_get_attachment_metadata( '', $post_id ) );

		update_post_meta( $post_id, '_wp_attached_file', 'file.jpg' );
		$this->assertSame( 'file.jpg', $this->property->wp_get_attachment_metadata( null, $post_id ) );
		$this->assertSame( 'file.jpg', $this->property->wp_get_attachment_metadata( [], $post_id ) );
		$this->assertSame( 'file.jpg', $this->property->wp_get_attachment_metadata( '', $post_id ) );
	}

	public function test_property_wp_get_attachment_metadata_wrong_values() {
		$this->assertSame( true, $this->property->wp_get_attachment_metadata( true, 0 ) );
		$this->assertSame( false, $this->property->wp_get_attachment_metadata( false, 0 ) );
		$this->assertEquals( new stdClass, $this->property->wp_get_attachment_metadata( new stdClass, 0 ) );
		$this->assertSame( 1, $this->property->wp_get_attachment_metadata( 1, 0 ) );
	}
}
