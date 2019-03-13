<?php

class Papi_Lib_Core_Slug_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET  = [];
		$_POST = [];
	}

	public function tearDown() {
		parent::tearDown();
		$_GET = [];
		$_POST = [];
	}

	public function test_papify() {
		$this->assertSame( 'papi_hello_world', papify( 'hello_world' ) );
		$this->assertSame( 'papi_hello_world', papify( 'papi_hello_world' ) );
		$this->assertSame( 'papi_hello_world', papify( '_hello_world' ) );
		$this->assertEmpty( papify( null ) );
		$this->assertEmpty( papify( true ) );
		$this->assertEmpty( papify( false ) );
		$this->assertEmpty( papify( 1 ) );
		$this->assertEmpty( papify( [] ) );
		$this->assertEmpty( papify( new stdClass ) );
	}

	public function test_unpapify() {
		$this->assertSame( 'hello-world', unpapify( 'papi-hello-world' ) );
		$this->assertEmpty( unpapify( null ) );
		$this->assertEmpty( unpapify( true ) );
		$this->assertEmpty( unpapify( false ) );
		$this->assertEmpty( unpapify( 1 ) );
		$this->assertEmpty( unpapify( [] ) );
		$this->assertEmpty( unpapify( new stdClass() ) );
	}
}
