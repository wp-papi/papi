<?php

/**
 * @group core
 */
class Papi_Core_Tab_Test extends WP_UnitTestCase {

	public function test_options() {
		$tab = new Papi_Core_Tab( [
			'title' => 'Tab'
		] );
		$this->assertSame( 'Tab', $tab->title );
		$this->assertSame( '_papi_tab', $tab->id );
	}

	public function test_excluded_options() {
		$tab = new Papi_Core_Tab( [
			'title'      => 'Tab',
			'options'    => null,
			'properties' => null,
		] );
		$this->assertSame( 'Tab', $tab->title );
		$this->assertSame( '_papi_tab', $tab->id );
		$this->assertSame( [], $tab->properties );
	}
}
