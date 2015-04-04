<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering tabs functionality.
 *
 * @package Papi
 */

class WP_Papi_Functions_Tabs extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

	}

	/**
	 * Test papi_get_tab_options.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_tab_options() {
		$tab = papi_tab(array(
			'title' => 'Content'
		));

		$options = papi_get_tab_options($tab->options);

		$this->assertEquals( $tab->options['title'], $options->title );
		$this->assertEquals( 1000, $options->sort_order );

		$tab = array(
			'title' => 'Content'
		);

		$options = papi_get_tab_options($tab);

		$this->assertEquals( $tab['title'], $options->title );
		$this->assertEquals( 1000, $options->sort_order );
	}

	/**
	 * Test papi_setup_tabs.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_setup_tabs() {
		$tab  = papi_tab('Content');
		$tabs = papi_setup_tabs(array(
			$tab
		));

		$this->assertEquals( $tab->options->title, $tabs[0]->options->title );
		$this->assertEquals( 1000, $tabs[0]->options->sort_order );
	}

	/**
	 * Test papi_tab.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_tab() {
		$actual = papi_tab( 'Content', array(
			papi_property( array(
				'type'  => 'string',
				'title' => 'Name'
			) )
		) );

		$this->assertTrue( $actual->tab );
		$this->assertEquals( 'Content', $actual->options['title'] );
		$this->assertEquals( 'Name', $actual->properties[0]->title );
		$this->assertEquals( 'string', $actual->properties[0]->type );
	}

}
