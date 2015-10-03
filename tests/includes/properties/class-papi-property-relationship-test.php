<?php

/**
 * Unit tests covering property relationship.
 *
 * @package Papi
 */
class Papi_Property_Relationship_Test extends Papi_Property_Test_Case {

	public $slug = 'relationship_test';

	public function get_value() {
		return [$this->post_id];
	}

	public function get_expected() {
		return [get_post( $this->post_id )];
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertEquals( [9230], $this->property->format_value( [9230], '', 0 ) );
		$this->assertEmpty( $this->property->format_value( [], '', 0 ) );
		$this->assertEmpty( $this->property->format_value( null, '', 0 ) );
		$this->assertEmpty( $this->property->format_value( true, '', 0 ) );
		$this->assertEmpty( $this->property->format_value( false, '', 0 ) );
		$this->assertEmpty( $this->property->format_value( 'hello', '', 0 ) );
	}

	public function test_property_import_value() {
		$output = $this->property->import_value( [], '', 0 );
		$this->assertEmpty( $output );

		$output = $this->property->import_value( (object) [], '', 0 );
		$this->assertEmpty( $output );

		$output = $this->property->import_value( $this->post_id, '', 0 );
		$this->assertEquals( $this->get_value(), $output );

		$output = $this->property->import_value( $this->get_value(), '', 0 );
		$this->assertEquals( $this->get_value(), $output );

		$output = $this->property->import_value( $this->get_expected(), '', 0 );
		$this->assertEquals( $this->get_value(), $output );

		$this->assertNull( $this->property->import_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
	}

	public function test_get_sort_options_name_alphabetically() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta'] );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		usort( $arr, $sort_options['Name (alphabetically)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_created_date_ascending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_date' => '2015-01-01 12:00', 'post_date_gmt' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_date' => '2015-01-01 15:00', 'post_date_gmt' => '2015-01-01 15:00'] );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		usort( $arr, $sort_options['Post created date (ascending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_created_date_descending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_date' => '2015-01-01 12:00', 'post_date_gmt' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_date' => '2015-01-01 15:00', 'post_date_gmt' => '2015-01-01 15:00'] );

		$arr = [get_post( $post_id ), get_post( $post_id2 )];
		$out = [get_post( $post_id2 ), get_post( $post_id )];
		usort( $arr, $sort_options['Post created date (descending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_id_ascending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta'] );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		usort( $arr, $sort_options['Post id (ascending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_id_descending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta'] );

		$arr = [get_post( $post_id ), get_post( $post_id2 )];
		$out = [get_post( $post_id2 ), get_post( $post_id )];
		usort( $arr, $sort_options['Post id (descending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_order_value_ascending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'menu_order' => 3] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'menu_order' => 5] );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		usort( $arr, $sort_options['Post order value (ascending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_order_value_descending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'menu_order' => 3] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'menu_order' => 5] );

		$arr = [get_post( $post_id ), get_post( $post_id2 )];
		$out = [get_post( $post_id2 ), get_post( $post_id )];
		usort( $arr, $sort_options['Post order value (descending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_modified_date_ascending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_modified' => '2015-01-01 12:00', 'post_modified_gmt' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_modified' => '2015-01-01 15:00', 'post_modified_gmt' => '2015-01-01 15:00'] );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		usort( $arr, $sort_options['Post modified date (ascending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_modified_date_descending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_modified' => '2015-01-01 12:00', 'post_modified_gmt' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_modified' => '2015-01-01 15:00', 'post_modified_gmt' => '2015-01-01 15:00'] );

		$arr = [get_post( $post_id ), get_post( $post_id2 )];
		$out = [get_post( $post_id2 ), get_post( $post_id )];
		usort( $arr, $sort_options['Post modified date (descending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_property_load_value() {
		$this->assertEquals( ['yes' => true], $this->property->load_value( '{"yes":true}', '', 0 ) );
		$this->assertEquals( [], $this->property->load_value( '{}', '', 0 ) );
		$this->assertEquals( [1, 2, 3], $this->property->load_value( '[1, 2, 3]', '', 0 ) );
		$this->assertEquals( [], $this->property->load_value( '[]', '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'relationship', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Relationship test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_relationship_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_sort_value() {
		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta'] );

		$slug = $this->property->html_id( 'sort_option' );
		update_post_meta( $post_id, $slug, 'Name (alphabetically)' );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		$this->assertEquals( $out, $this->property->sort_value( $arr, '', $post_id ) );
	}
}
