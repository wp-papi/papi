<?php

/**
 * @group properties
 */
class Papi_Property_Relationship_Test extends Papi_Property_Test_Case {

	public $slugs = ['relationship_test', 'relationship_test_2'];

	public function assert_values( $expected, $actual ) {
		if ( isset( $expected[0]->ID ) ) {
			$this->assertSame( $expected[0]->ID, $actual[0]->ID );
		} else {
			$this->assertSame( $expected[0]->id, $actual[0]->id );
		}
	}

	public function get_value() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'relationship_test_2':
				return [1];
			default:
				return [$this->post_id];
		}
	}

	public function get_expected() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'relationship_test_2':
				return [
					(object) [
						'id'    => 1,
						'title' => 'One'
					]
				];
			default:
				return [get_post( $this->post_id )];
		}
	}

	public function test_property_convert_type() {
		$this->assertSame( 'array', $this->properties[0]->convert_type );
		$this->assertSame( 'array', $this->properties[1]->convert_type );
	}

	public function test_property_default_value() {
		$this->assertSame( [], $this->properties[0]->default_value );
		$this->assertSame( [], $this->properties[1]->default_value );
	}

	public function test_property_format_value() {
		$this->assertEmpty( $this->properties[0]->format_value( [9230], '', 0 ) );
		$this->assertEmpty( $this->properties[0]->format_value( [], '', 0 ) );
		$this->assertEmpty( $this->properties[0]->format_value( ['id' => null], '', 0 ) );
		$this->assertEmpty( $this->properties[0]->format_value( null, '', 0 ) );
		$this->assertEmpty( $this->properties[0]->format_value( true, '', 0 ) );
		$this->assertEmpty( $this->properties[0]->format_value( false, '', 0 ) );
		$this->assertEmpty( $this->properties[0]->format_value( 'hello', '', 0 ) );

		$out = $this->properties[0]->format_value( [$this->post_id], '', 0 );
		$this->assertEquals( [get_post( $this->post_id )], $out );
	}

	public function test_property_format_value_custom_items() {
		$categories = array_map( function ( $cat ) {
		  return [
		    'id'    => (int) $cat->term_id,
		    'title' => $cat->name
		  ];
		}, get_categories() );

		$property = papi_property( [
			'type'     => 'relationship',
			'title'    => 'Relationship',
			'settings' => [
				'items' => $categories
			]
		] );

		$out = $property->format_value( [
			[
				'id'    => 1,
				'title' => 'Uncategorized'
			]
		], '', 0 );

		$this->assertEquals( [
			(object) [
				'id'    => 1,
				'title' => 'Uncategorized'
			]
		], $out );

		$out = $property->format_value( [
			(object) [
				'id'    => 1,
				'title' => 'Uncategorized'
			]
		], '', 0 );

		$this->assertEquals( [
			(object) [
				'id'    => 1,
				'title' => 'Uncategorized'
			]
		], $out );

		$out = $property->format_value( [
			(object) [
				'id'    => null,
				'title' => 'Uncategorized'
			]
		], '', 0 );

		$this->assertEmpty( $out );
	}

	public function test_property_import_value() {
		$output = $this->properties[0]->import_value( [], '', 0 );
		$this->assertEmpty( $output );

		$output = $this->properties[0]->import_value( (object) [], '', 0 );
		$this->assertEmpty( $output );

		$output = $this->properties[0]->import_value( $this->post_id, '', 0 );
		$this->assertSame( $this->get_value( 'relationship_test' ), $output );

		$output = $this->properties[0]->import_value( (object) ['id' => $this->post_id], '', 0 );
		$this->assertSame( $this->get_value( 'relationship_test' ), $output );

		$output = $this->properties[0]->import_value( $this->get_value( 'relationship_test' ), '', 0 );
		$this->assertSame( $this->get_value( 'relationship_test' ), $output );

		$output = $this->properties[0]->import_value( $this->get_expected( 'relationship_test' ), '', 0 );
		$this->assertSame( $this->get_value( 'relationship_test' ), $output );

		$this->assertNull( $this->properties[0]->import_value( 'hello', '', 0 ) );
		$this->assertNull( $this->properties[0]->import_value( null, '', 0 ) );
		$this->assertNull( $this->properties[0]->import_value( true, '', 0 ) );
		$this->assertNull( $this->properties[0]->import_value( false, '', 0 ) );
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

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_date' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_date' => '2015-01-01 15:00'] );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		usort( $arr, $sort_options['Post created date (ascending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_created_date_descending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_date' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_date' => '2015-01-01 15:00'] );

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

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_date' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_date' => '2015-01-01 15:00'] );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		usort( $arr, $sort_options['Post modified date (ascending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_get_sort_options_post_modified_date_descending() {
		$sort_options = Papi_Property_Relationship::get_sort_options();

		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa', 'post_date' => '2015-01-01 12:00'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta', 'post_date' => '2015-01-01 15:00'] );

		$arr = [get_post( $post_id ), get_post( $post_id2 )];
		$out = [get_post( $post_id2 ), get_post( $post_id )];
		usort( $arr, $sort_options['Post modified date (descending)'] );
		$this->assertEquals( $out, $arr );
	}

	public function test_property_load_value() {
		$this->assertSame( ['yes' => true], $this->properties[0]->load_value( '{"yes":true}', '', 0 ) );
		$this->assertSame( [], $this->properties[0]->load_value( '{}', '', 0 ) );
		$this->assertSame( [1, 2, 3], $this->properties[0]->load_value( '[1, 2, 3]', '', 0 ) );
		$this->assertSame( [], $this->properties[0]->load_value( '[]', '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'relationship', $this->properties[0]->get_option( 'type' ) );
		$this->assertSame( 'Relationship test', $this->properties[0]->get_option( 'title' ) );
		$this->assertSame( 'papi_relationship_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertSame( 'relationship', $this->properties[1]->get_option( 'type' ) );
		$this->assertSame( 'Relationship test 2', $this->properties[1]->get_option( 'title' ) );
		$this->assertSame( 'papi_relationship_test_2', $this->properties[1]->get_option( 'slug' ) );
	}

	public function test_property_sort_value() {
		$post_id  = $this->factory->post->create( ['post_title' => 'Alfa'] );
		$post_id2 = $this->factory->post->create( ['post_title' => 'Beta'] );

		$slug = $this->properties[0]->html_id( 'sort_option' );
		update_post_meta( $post_id, unpapify( $slug ) , 'Name (alphabetically)' );

		$arr = [get_post( $post_id2 ), get_post( $post_id )];
		$out = [get_post( $post_id ), get_post( $post_id2 )];
		$this->assertEquals( $out, $this->properties[0]->sort_value( $arr, '', $post_id ) );
	}

	public function test_property_sort_value_2() {
		$post_id = $this->factory->post->create( ['post_title' => 'Alfa'] );
		$slug    = $this->properties[1]->html_id( 'sort_option' );
		update_post_meta( $post_id, unpapify( $slug ), 'Name (alphabetically)' );

		$arr = $this->properties[1]->get_setting( 'items' );
		$arr = array_map( function( $a ) {
			return (object) $a;
		}, $arr );
		$out = array_reverse( $arr );
		$this->assertEquals( $out, $this->properties[1]->sort_value( $arr, '', $post_id ) );
	}
}
