<?php

// Load Composer autoload.
require dirname( __DIR__ ) . '/vendor/autoload.php';

// Define fixtures directory constant
define( 'PAPI_FIXTURE_DIR', __DIR__ . '/fixtures' );

// Load Papi loader file as plugin.
WP_Test_Suite::load_plugins( dirname( __DIR__ ) . '/papi-loader.php' );

// Load our helpers file.
WP_Test_Suite::load_files( [
	__DIR__ . '/helpers.php',
	__DIR__	. '/class-papi-property-test-case.php'
] );

// Run the WordPress test suite.
WP_Test_Suite::run();
