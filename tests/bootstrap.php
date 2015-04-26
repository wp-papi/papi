<?php

/**
 * Load Composer autoloader.
 */

require dirname( __DIR__ ) . '/vendor/autoload.php';

/**
 * Load Papi loader file as plugin.
 */

WP_Test_Suite::load_plugins( dirname( __DIR__ ) . '/papi-loader.php' );

/**
 * Load our helpers file.
 */

WP_Test_Suite::load_files( __DIR__ . '/helpers.php' );

/**
 * Run the WordPress test suite.
 */

WP_Test_Suite::run();
