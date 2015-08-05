<?php

/**
 * Plugin Name: Papi
 * Description: Page Type API
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me
 * Version: 2.3.0-dev
 * Plugin URI: https://wp-papi.github.io
 * Textdomain: papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Load Composer autoload if it exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Load Papi loader.
require_once __DIR__ . '/src/papi-loader.php';
