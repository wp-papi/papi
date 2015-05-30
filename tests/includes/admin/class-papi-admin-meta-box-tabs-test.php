<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin_Meta_Box_Tabs` class.
 *
 * @package Papi
 */

class Papi_Admin_Meta_Box_Tabs_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->tabs = [
			papi_tab( [ 'title' => 'Content' ] ),
			papi_tab( [ 'title' => 'More', 'sort_order' => 1 ], [
				papi_property( [
					'type'  => 'string',
					'title' => 'Name'
				] )
			] )
		];
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->tabs );
	}

	public function test_construct() {
		$class = new Papi_Admin_Meta_Box_Tabs();
		$this->assertEmpty($class->get_tabs());
	}

	public function test_tabs() {
		// "Content" is tab nr 1.
		$this->assertEquals( 'Content', $this->tabs[0]->options['title'] );
		$this->assertEmpty( $this->tabs[0]->properties );

		// "More" is tab nr 2.
		$this->assertEquals( 'More', $this->tabs[1]->options['title'] );

		// "More" tab should have a property.
		$this->assertEquals( 'string', $this->tabs[1]->properties[0]->type );
		$this->assertEquals( 'Name', $this->tabs[1]->properties[0]->title );
	}

	public function test_papi_admin_meta_box_tab_class() {
		$class = new Papi_Admin_Meta_Box_Tabs( $this->tabs, false );
		$tabs  = $class->get_tabs();

		// "More" is now tab nr 1.
		$this->assertEquals( 'More', $tabs[0]->options->title );
		$this->assertEquals( 1, $tabs[0]->options->sort_order );

		// "More" tab should have a property.
		$this->assertEquals( 'string', $tabs[0]->properties[0]->type );
		$this->assertEquals( 'Name', $tabs[0]->properties[0]->title );

		// "Content" is now tab nr 2.
		$this->assertEquals( 'Content', $tabs[1]->options->title );
		$this->assertEquals( 1000, $tabs[1]->options->sort_order );
		$this->assertEmpty( $tabs[1]->properties );
	}
}
