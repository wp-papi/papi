<?php

/**
 * Get all files in directory.
 *
 * @param  string $directory
 *
 * @return string
 */
function papi_get_all_files_in_directory( $directory = '' ) {
	$result = [];

	if ( empty( $directory ) || ! is_string( $directory ) ) {
		return $result;
	}

	if ( file_exists( $directory ) && $handle = opendir( $directory ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( ! in_array( $file, ['..', '.'], true ) ) {
				if ( is_dir( $directory . '/' . $file ) ) {
					$result   = array_merge( $result, papi_get_all_files_in_directory( $directory . '/' . $file ) );
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
 * Get core type file path, this allows classes to be overridden.
 *
 * @param  string $file_path
 *
 * @return string
 */
function papi_get_core_type_file_path( $file_path ) {
	if ( empty( $file_path ) ) {
		return [];
	}

	$directories = papi_filter_settings_directories();
	$result      = [];

	foreach ( $directories as $directory ) {
		$directory = rtrim( $directory, '/' ) . '/';
		$file_path = str_replace( $directory, '', $file_path );
		$path      = $directory . $file_path;

		if ( file_exists( $path ) ) {
			$result[] = $path;
		}
	}

	return array_pop( $result );
}

/**
 * Get all entry type files from the register directories.
 *
 * @return array
 */
function papi_get_all_core_type_files() {
	return papi()->once( __FUNCTION__, function() {
		$directories = papi_filter_settings_directories();
		$result      = [];

		foreach ( $directories as $directory ) {
			$result = array_merge( $result, papi_get_all_files_in_directory( $directory ) );
		}

		// Get the last file path from directories.
		$result = array_map( 'papi_get_core_type_file_path', $result );

		// Only unique path, no duplicated path is allowed.
		return array_unique( $result );
	} );
}

/**
 * Get page type file from page type query.
 *
 * @param  string $file
 *
 * @return null|string
 */
function papi_get_file_path( $file ) {
	if ( empty( $file ) || ! is_string( $file ) ) {
		return;
	}

	$directories = papi_filter_settings_directories();
	$file        = '/' . str_replace( ' ', '-', str_replace( '_', '-', $file ) );

	foreach ( $directories as $directory ) {
		if ( file_exists( $directory . $file ) ) {
			return $directory . $file;
		}

		if ( file_exists( $directory . $file . '.php' ) ) {
			return $directory . $file . '.php';
		}
	}
}

/**
 * Get entry type base path.
 *
 * @param  string $file
 *
 * @return string
 */
function papi_get_core_type_base_path( $file ) {
	if ( empty( $file ) || ! is_string( $file ) ) {
		return '';
	}

	$directories = papi_filter_settings_directories();

	foreach ( $directories as $directory ) {
		if ( strpos( $file, $directory ) !== false ) {
			$file = str_replace( $directory . '/', '', $file );
		}
	}

	$file = explode( '.', $file );

	return $file[0];
}
