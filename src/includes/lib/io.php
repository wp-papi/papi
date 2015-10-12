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

	if ( $handle = opendir( $directory ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( ! in_array( $file, ['..', '.'] ) ) {
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
 * Get all page type files from the register directories.
 *
 * @return array
 */
function papi_get_all_page_type_files() {
	$directories = papi_filter_settings_directories();
	$result      = [];

	foreach ( $directories as $directory ) {
		$result = array_merge( $result, papi_get_all_files_in_directory( $directory ) );
	}

	return $result;
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
	$file        = '/' . papi_dashify( $file );

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
 * Get page type base path.
 *
 * @param  string $file
 *
 * @return null|string
 */
function papi_get_page_type_base_path( $file ) {
	if ( empty( $file ) || ! is_string( $file ) ) {
		return;
	}

	$directories = papi_filter_settings_directories();

	foreach ( $directories as $directory ) {
		if ( strpos( $file, $directory ) !== false ) {
			$file = str_replace( $directory, '', $file );
		}
	}

	$file = ltrim( $file, '/' );
	$file = explode( '.', $file );

	return $file[0];
}
