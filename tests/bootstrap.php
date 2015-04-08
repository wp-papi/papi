<?php

/**
 * Bootstrap the plugin unit testing environment.
 *
 * @package Papi
 */

// If the develop repo location is defined (as WP_DEVELOP_DIR), use that
// location. Otherwise, we'll just assume that this plugin is installed in a
// WordPress develop SVN checkout.

if ( getenv( 'WP_DEVELOP_DIR' ) !== false ) {
	$test_root = getenv( 'WP_DEVELOP_DIR' );
} else if ( file_exists( '../../../../tests/phpunit/includes/bootstrap.php' ) ) {
	$test_root = '../../../../';
} else if ( file_exists( '/tmp/wordpress-tests-lib/includes/bootstrap.php' ) ) {
	$test_root = '/tmp/wordpress-tests-lib/';
} else if ( file_exists( '../../../develop/tests/phpunit/includes/bootstrap.php' ) ) {
	$test_root = '../../../develop/tests/phpunit/';
}

// Load WordPress test functions.
require $test_root . '/includes/functions.php';

// Load Papi.
tests_add_filter( 'muplugins_loaded', function () {
	require dirname( __DIR__ ) . '/papi-loader.php';
} );

// Load phpunit.
require $test_root . '/includes/bootstrap.php';

// Load helpers file for testing.
require __DIR__ . '/helpers.php';
