<?php

class Papi_Lib_Actions_Test extends WP_UnitTestCase {

	public function test_papi_action_include() {
		papi_action_include();
		$this->assertNotFalse( did_action( 'papi/include' ) );
	}

	public function test_papi_action_delete_value() {
		papi_action_delete_value( 'string', 'name', 0 );
		$this->assertNotFalse( did_action( 'papi/delete_value/string' ) );
		papi_action_delete_value( 'number', 'name' );
		$this->assertNotFalse( did_action( 'papi/delete_value/number' ) );
	}
}
