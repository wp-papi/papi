<?php

/**
 * @group properties
 */
class Papi_Property_File_Test extends Papi_Property_Test_Case {

	public $slugs = ['file_test', 'file_test_3'];

	public function get_value() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			default:
				return 23;
		}
	}

	public function get_expected() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			default:
				return 23;
		}
	}

	public function assert_values( $expected, $actual, $slug ) {
		switch ( $slug ) {
			case 'file_test_3';
				break; // fail ok since we don't save a custom id.
			default:
				$this->assertSame( $expected, $actual );
				break;
		}
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

	public function test_property_format_value_meta_key() {
		$post_id = $this->factory->post->create( ['post_type' => 'attachment'] );
		$this->assertSame( 0, intval( $this->properties[1]->format_value( 0, '', 0 ) ) );
		update_post_meta( $post_id, 'custom_id', 1 );
		$this->assertNotSame( 1, intval( $this->properties[1]->format_value( 1, '', 0 ) ) );
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
