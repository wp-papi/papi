<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin_Meta_Box_Tabs` class.
 *
 * @package Papi
 */

class Papi_Admin_Meta_Box_Tabs_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$this->tabs = array(
			papi_tab( array('title' => 'Content') ),
			papi_tab( array('title' => 'More', 'sort_order' => 1 ), array(
				papi_property( array(
					'type'  => 'string',
					'title' => 'Name'
				) )
			) )
		);
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->tabs );
	}

	/**
	 * Test the tabs array.
	 * The tabs aren't sorted yet with sort order key.
	 *
	 * @since 1.0.0
	 */

	public function test_tabs () {
		// "Content" is tab nr 1.
		$this->assertEquals( 'Content', $this->tabs[0]->options['title'] );
		$this->assertEmpty( $this->tabs[0]->properties );

		// "More" is tab nr 2.
		$this->assertEquals( 'More', $this->tabs[1]->options['title'] );

		// "More" tab should have a property.
		$this->assertEquals( 'string', $this->tabs[1]->properties[0]->type );
		$this->assertEquals( 'Name', $this->tabs[1]->properties[0]->title );
	}

	/**
	 * Test Papi_Admin_Meta_Box_Tabs class.
	 * Here are the tabs sorted with sort order key.
	 *
	 * @since 1.0.0
	 */

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
