<?php

/**
 * Bootstrap the plugin unit testing environment.
 *
 * Edit 'active_plugins' setting below to point to your main plugin file.
 *
 * @package PageTypeBuilder
 */

// Activates this plugin in WordPress so it can be tested.
$GLOBALS['wp_tests_options'] = array(
  'active_plugins' => array('page-type-builder/ptb-loader.php'),
);

// If the develop repo location is defined (as WP_DEVELOP_DIR), use that
// location. Otherwise, we'll just assume that this plugin is installed in a
// WordPress develop SVN checkout.

if (getenv('WP_DEVELOP_DIR') !== false) {
  require getenv('WP_DEVELOP_DIR') . '/tests/phpunit/includes/bootstrap.php';
} else {
  //require '../../../../tests/phpunit/includes/bootstrap.php';
  require '../../../develop/tests/phpunit/includes/bootstrap.php';
}

// Load utilities file for testing.
require 'lib/utilities.php';