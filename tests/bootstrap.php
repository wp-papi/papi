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
  $test_root = getenv('WP_DEVELOP_DIR');
} else if (file_exists('../../../../tests/phpunit/includes/bootstrap.php')) {
  $test_root = '../../../../includes';
} else if (file_exists('/tmp/wordpress-tests-lib/includes/bootstrap.php')) {
  $test_root = '/tmp/wordpress-tests-lib/includes';
} else if (file_exists('../../../develop/tests/phpunit/includes/bootstrap.php')) {
  $test_root = '../../../develop/tests/phpunit/includes';
}

// Load phpunit
require $test_root . '/bootstrap.php';

// Load utilities file for testing.
require 'lib/utilities.php';