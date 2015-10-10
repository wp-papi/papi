<?php

/**
 * Unit tests covering tabs functions.
 *
 * @package Papi
 */
class Papi_Lib_Tabs_Test extends WP_UnitTestCase {

	/**
	 * Test papi_get_tab_options.
	 */
	public function test_papi_get_tab_options() {
		$tab = papi_tab( [
			'title' => 'Content'
		] );

		$options = papi_get_tab_options( $tab->options );
		$this->assertSame( $tab->options['title'], $options->title );
		$this->assertSame( 1000, $options->sort_order );

		$tab = [
			'title' => 'Content'
		];

		$options = papi_get_tab_options( $tab );
		$this->assertSame( $tab['title'], $options->title );
		$this->assertSame( 1000, $options->sort_order );

		$tab = papi_tab( [
			'title' => 'Content'
		] );

		$options = papi_get_tab_options( $tab );
		$this->assertSame( $tab->options['title'], $options->options['title'] );
		$this->assertSame( 1000, $options->sort_order );

		$this->assertNull( papi_get_tab_options( [] ) );
		$this->assertNull( papi_get_tab_options( null ) );
		$this->assertNull( papi_get_tab_options( 1 ) );
		$this->assertNull( papi_get_tab_options( true ) );
		$this->assertNull( papi_get_tab_options( false ) );
		$this->assertNull( papi_get_tab_options( 'Title' ) );
	}

	/**
	 * Test papi_setup_tabs.
	 */
	public function test_papi_setup_tabs() {
		$tab  = papi_tab( 'Content' );
		$tabs = papi_setup_tabs( [$tab] );

		$this->assertSame( $tab->options->title, $tabs[0]->options->title );
		$this->assertSame( 1000, $tabs[0]->options->sort_order );

		$tabs = papi_setup_tabs( [1] );
		$this->assertEmpty( $tabs );

		$tabs = papi_setup_tabs( [] );
		$this->assertEmpty( $tabs );
	}

	/**
	 * Test papi_tab.
	 */
	public function test_papi_tab() {
		$actual = papi_tab( 'Content', [
			papi_property( [
				'type'  => 'string',
				'title' => 'Name'
			] )
		] );

		$this->assertTrue( $actual->tab );
		$this->assertSame( 'Content', $actual->options['title'] );
		$this->assertSame( 'Name', $actual->properties[0]->title );
		$this->assertSame( 'string', $actual->properties[0]->type );
	}
}
