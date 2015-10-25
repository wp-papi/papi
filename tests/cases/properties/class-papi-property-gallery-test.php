<?php

class Papi_Property_Gallery_Test extends Papi_Property_Test_Case {

	public $slug = 'gallery_test';

	public function get_value() {
		return [23];
	}

	public function get_expected() {
		return [23];
	}

	public function test_property_convert_type() {
		$this->assertSame( 'array', $this->property->convert_type );
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

		$images = $this->property->format_value( [$post_id, $post_id], '', $post_id );

		$this->assertTrue( is_object( $images[0] ) );
		$this->assertSame( 2900, $images[0]->width );
		$this->assertSame( 1559, $images[0]->height );
		$this->assertSame( '2011/12/press_image.jpg', $images[0]->file );

		$this->assertTrue( is_array( $images[0]->sizes ) );
		$this->assertTrue( isset( $images[0]->sizes['thumbnail'] ) );
		$this->assertSame( 'press_image-150x150.jpg', $images[0]->sizes['thumbnail']['file'] );
		$this->assertSame( 150, $images[0]->sizes['thumbnail']['width'] );
		$this->assertSame( 150, $images[0]->sizes['thumbnail']['height'] );
		$this->assertSame( 'image/jpeg', $images[0]->sizes['thumbnail']['mime-type'] );
		$this->assertSame( $thumbnail_url, $images[0]->sizes['thumbnail']['url'] );

		$this->assertTrue( is_array( $images[0]->image_meta ) );
		$this->assertSame( 5, $images[0]->image_meta['aperture'] );
		$this->assertSame( '', $images[0]->image_meta['credit'] );
		$this->assertSame( 'Super', $images[0]->image_meta['camera'] );
		$this->assertSame( '', $images[0]->image_meta['caption'] );
		$this->assertSame( 1323190643, $images[0]->image_meta['created_timestamp'] );
		$this->assertSame( '', $images[0]->image_meta['copyright'] );
		$this->assertSame( 35, $images[0]->image_meta['focal_length'] );
		$this->assertSame( 800, $images[0]->image_meta['iso'] );
		$this->assertSame( 0.016666666666667, $images[0]->image_meta['shutter_speed'] );
		$this->assertSame( '', $images[0]->image_meta['title'] );

		$this->assertTrue( is_object( $images[1] ) );
		$this->assertSame( 2900, $images[1]->width );
		$this->assertSame( 1559, $images[1]->height );
		$this->assertSame( '2011/12/press_image.jpg', $images[1]->file );

		$this->assertTrue( is_array( $images[1]->sizes ) );
		$this->assertTrue( isset( $images[1]->sizes['thumbnail'] ) );
		$this->assertSame( 'press_image-150x150.jpg', $images[1]->sizes['thumbnail']['file'] );
		$this->assertSame( 150, $images[1]->sizes['thumbnail']['width'] );
		$this->assertSame( 150, $images[1]->sizes['thumbnail']['height'] );
		$this->assertSame( 'image/jpeg', $images[1]->sizes['thumbnail']['mime-type'] );
		$this->assertSame( $thumbnail_url, $images[1]->sizes['thumbnail']['url'] );

		$this->assertTrue( is_array( $images[1]->image_meta ) );
		$this->assertSame( 5, $images[1]->image_meta['aperture'] );
		$this->assertSame( '', $images[1]->image_meta['credit'] );
		$this->assertSame( 'Super', $images[1]->image_meta['camera'] );
		$this->assertSame( '', $images[1]->image_meta['caption'] );
		$this->assertSame( 1323190643, $images[1]->image_meta['created_timestamp'] );
		$this->assertSame( '', $images[1]->image_meta['copyright'] );
		$this->assertSame( 35, $images[1]->image_meta['focal_length'] );
		$this->assertSame( 800, $images[1]->image_meta['iso'] );
		$this->assertSame( 0.016666666666667, $images[1]->image_meta['shutter_speed'] );
		$this->assertSame( '', $images[1]->image_meta['title'] );
	}

	public function test_property_import_value() {
		$this->assertEmpty( $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'gallery', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Gallery test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_gallery_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertTrue( $this->property->get_setting( 'multiple' ) );
	}
}
