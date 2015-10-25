<?php

class Papi_Property_Image_Test extends Papi_Property_Test_Case {

	public $slug = 'image_test';

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
		$post_id = $this->factory->post->create( ['post_type' => 'attachment', 'post_mime_type' => 'image/jpeg'] );
		$meta    = [
			'width'      => 2900,
			'height'     => 1559,
			'file'       => '2011/12/press_image.jpg',
			'sizes'      => [
				'thumbnail' => [
					'file'      => 'press_image-150x150.jpg',
					'width'     => 150,
					'height'    => 150,
					'mime-type' => 'image/jpeg'
				]
			],
			'image_meta' => [
				'aperture'          => 5,
				'credit'            => '',
				'camera'            => 'Super',
				'caption'           => '',
				'created_timestamp'	=> 1323190643,
				'copyright'         => '',
				'focal_length'      => 35,
				'iso'               => 800,
				'shutter_speed'     => 0.016666666666667,
				'title'             => ''
			]
		];

		update_post_meta( $post_id, '_wp_attachment_metadata', $meta );
		update_post_meta( $post_id, '_wp_attachment_image_alt', 'alt text' );
		update_post_meta( $post_id, '_wp_attached_file', '2011/12/press_image.jpg' );
		$thumbnail_url = home_url( '/wp-content/uploads/2011/12/press_image-150x150.jpg' );

		tests_add_filter( 'image_downsize', function( $image, $attachment_id, $size ) use ( $thumbnail_url ) {
			return [$thumbnail_url, 150, 150, false];
		}, 10, 3 );

		$image = $this->property->format_value( $post_id, '', $post_id );

		$this->assertTrue( is_object( $image ) );
		$this->assertSame( 2900, $image->width );
		$this->assertSame( 1559, $image->height );
		$this->assertSame( '2011/12/press_image.jpg', $image->file );

		$this->assertTrue( is_array( $image->sizes ) );
		$this->assertTrue( isset( $image->sizes['thumbnail'] ) );
		$this->assertSame( 'press_image-150x150.jpg', $image->sizes['thumbnail']['file'] );
		$this->assertSame( 150, $image->sizes['thumbnail']['width'] );
		$this->assertSame( 150, $image->sizes['thumbnail']['height'] );
		$this->assertSame( 'image/jpeg', $image->sizes['thumbnail']['mime-type'] );
		$this->assertSame( $thumbnail_url, $image->sizes['thumbnail']['url'] );

		$this->assertTrue( is_array( $image->image_meta ) );
		$this->assertSame( 5, $image->image_meta['aperture'] );
		$this->assertSame( '', $image->image_meta['credit'] );
		$this->assertSame( 'Super', $image->image_meta['camera'] );
		$this->assertSame( '', $image->image_meta['caption'] );
		$this->assertSame( 1323190643, $image->image_meta['created_timestamp'] );
		$this->assertSame( '', $image->image_meta['copyright'] );
		$this->assertSame( 35, $image->image_meta['focal_length'] );
		$this->assertSame( 800, $image->image_meta['iso'] );
		$this->assertSame( 0.016666666666667, $image->image_meta['shutter_speed'] );
		$this->assertSame( '', $image->image_meta['title'] );
	}

	public function test_property_import_value() {
		$this->assertEmpty( $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'image', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Image test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_image_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertFalse( $this->property->get_setting( 'multiple' ) );
	}
}
