<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin_Meta_Box` class.
 *
 * @package Papi
 */

class Papi_Admin_Meta_Box_Test extends WP_UnitTestCase {

	public function test_construct() {
		$class = new Papi_Admin_Meta_Box();

        $class = new Papi_Admin_Meta_Box( [
            'title' => 'Content'
        ] );

        $class->setup_meta_box();
        do_meta_boxes( '_papi_content', 'normal', null );
        $this->expectOutputRegex( '//' );

        $class = new Papi_Admin_Meta_Box( [
            'title' => 'Content'
        ] );

        $class->setup_meta_box();
        do_meta_boxes( '_papi_content', 'normal', null );
        $this->expectOutputRegex( '//' );
	}

    public function test_meta_box_css_classes() {
		$class = new Papi_Admin_Meta_Box();
        $this->assertEquals( ['papi-box'], $class->meta_box_css_classes( [] ) );
    }

    public function test_add_property() {
        $class = new Papi_Admin_Meta_Box();
        $property = papi_property( [
            'type'  => 'string',
            'title' => 'Name'
        ] );
        $class->add_property( $property );
        $properties = function ( Papi_Admin_Meta_Box $box ) {
            return $box->properties;
        };
        $properties = Closure::bind( $properties, null, $class );
        $this->assertEquals( [$property], $properties( $class ) );
    }

    public function test_render_meta_box() {
        $class = new Papi_Admin_Meta_Box( [
            'title' => 'Content'
        ] );

        $this->assertNull( $class->render_meta_box( null, null ) );
        $this->assertNull( $class->render_meta_box( null, [] ) );

        $property = papi_property( [
            'type'  => 'string',
            'title' => 'Name'
        ] );

        $class->render_meta_box( null, [
            'args' => [$property]
        ] );

		$this->expectOutputRegex( '/.*\S.*/' );
    }

	public function test_required() {
        $property = papi_property( [
            'type'     => 'string',
            'title'    => 'Name',
            'required' => true
        ] );

        $class = new Papi_Admin_Meta_Box( [
            'title'     => 'Content',
            '_required' => true
        ], [$property] );

        $class->setup_meta_box();
        $title = function ( Papi_Admin_Meta_Box $box ) {
            return $box->options->title;
        };
        $title = Closure::bind( $title, null, $class );
        $this->assertEquals( sprintf(
            'Content <span class="papi-rq" data-property-name="%s" data-property-id="%s">%s</span>',
            $property->title,
            $property->slug,
            '(required field)'
        ), $title( $class ) );
	}

}
