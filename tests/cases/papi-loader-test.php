<?php

class Papi_Loader_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		add_filter( 'wp_die_ajax_handler', [$this, 'get_wp_die_handler'], 1, 1 );
	}

	public function tearDown() {
		parent::tearDown();
		remove_filter( 'wp_die_ajax_handler', [$this, 'get_wp_die_handler'], 1, 1 );
	}

	public function wp_die_handler( $message ) {
	}

	public function test_plugins_loaded_action() {
		$this->assertSame( 10, has_action( 'plugins_loaded', 'papi' ) );
	}

	public function test_papi_before_init_action() {
		global $wp_actions;

		Papi_Loader::deactivate();

		unset( $wp_actions['papi/before_init'] );

		add_action( 'papi/before_init', function () {
			$this->assertTrue( true );
		} );

		Papi_Loader::instance();
	}

	public function test_papi_init_action() {
		global $wp_actions;

		Papi_Loader::deactivate();

		unset( $wp_actions['papi/init'] );

		add_action( 'papi/init', function () {
			$this->assertTrue( true );
		} );

		Papi_Loader::instance();
	}

	public function test_papi_loaded_action() {
		global $wp_actions;

		Papi_Loader::deactivate();

		unset( $wp_actions['papi/loaded'] );

		add_action( 'papi/loaded', function () {
			$this->assertTrue( true );
		} );

		Papi_Loader::instance();
	}

	public function test_constants() {
		$this->assertTrue( defined( 'PAPI_PLUGIN_DIR' ) );
		$this->assertTrue( defined( 'PAPI_PLUGIN_URL' ) );
		$this->assertTrue( defined( 'PAPI_PAGE_TYPE_KEY' ) );
	}

	public function test_instance() {
		$this->assertClassHasStaticAttribute( 'instance', 'Papi_Loader' );
	}

	public function test_porter() {
		$papi = Papi_Loader::instance();
		$porter = $papi->porter();
		$this->assertTrue( $porter instanceof Papi_Porter );
	}

	public function test_plugin_activated() {
		$this->assertTrue( class_exists( 'Papi_Loader' ) && class_exists( 'Papi_Admin' ) );
	}

	public function test_name() {
		$papi = Papi_Loader::instance();
		$this->assertSame( 'Papi', $papi->name );
	}
}
