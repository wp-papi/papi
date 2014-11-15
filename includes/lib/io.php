<?php

/**
 * Papi I/O functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all files in directory.
 *
 * @param string $directory
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_all_files_in_directory( $directory = '' ) {
	$result = array();

	if ( $handle = opendir( $directory ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( ! in_array( $file, array( '..', '.' ) ) ) {
				if ( is_dir( $directory . '/' . $file ) ) {
					$result   = array_merge( $result, _papi_get_all_files_in_directory( $directory . '/' . $file ) );
					$file     = $directory . '/' . $file;
					$result[] = preg_replace( '/\/\//si', '/', $file );
				} else {
					$file     = $directory . '/' . $file;
					$result[] = preg_replace( '/\/\//si', '/', $file );
				}
			}
		}
		closedir( $handle );
	}

	return $result;
}

/**
 * Get all page type files from the register directories.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_get_all_page_type_files() {
	$directories = _papi_get_directories();
	$result      = array();

	foreach ( $directories as $directory ) {
		$result = array_merge( $result, _papi_get_all_files_in_directory( $directory ) );
	}

	return $result;
}

/**
 * Get all register directories with Papi.
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_get_directories() {
	global $papi_directories;

	if ( empty( $papi_directories ) || ! is_array( $papi_directories ) ) {
		return array();
	}

	return $papi_directories;
}

/**
 * Get page type file from page type query.
 *
 * @param string $file
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_file_path( $file ) {
	$directories = _papi_get_directories();
	$file        = '/' . _papi_dashify( $file );

	foreach ( $directories as $directory ) {
		if ( file_exists( $directory . $file ) ) {
			return $directory . $file;
		}

		if ( file_exists($directory . $file . '.php')) {
			return $directory . $file . '.php';
		}
	}

	return null;
}

/**
 * Get page type base path.
 * This is used for figure out which page type to load on which page.
 *
 * @param string $file
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_page_type_base_path( $file ) {
	$directories = _papi_get_directories();
	foreach ( $directories as $directory ) {
		if ( strpos( $file, $directory ) !== false ) {
			$file = str_replace( $directory, '', $file );
		}
	}
	$file = ltrim( $file, '/' );
	$file = explode( '.', $file );

	return $file[0];
}
