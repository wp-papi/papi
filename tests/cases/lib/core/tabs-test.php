<?php

class Papi_Lib_Core_Tabs_Test extends WP_UnitTestCase {

	/**
	 * Test papi_tabs_setup.
	 */
	public function test_papi_tabs_setup() {
		$tab  = papi_tab( 'Content' );
		$tabs = papi_tabs_setup( [$tab] );

		$this->assertSame( 'Content', $tabs[0]->title );
		$this->assertSame( 1000, $tabs[0]->sort_order );

		$tabs = papi_tabs_setup( [1] );
		$this->assertEmpty( $tabs );

		$tabs = papi_tabs_setup( [] );
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
		$this->assertSame( 'Content', $actual->title );
		$this->assertSame( 'Name', $actual->properties[0]->title );
		$this->assertSame( 'string', $actual->properties[0]->type );
	}
}
