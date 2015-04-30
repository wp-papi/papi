<?php

/**
 * Papi utilities functions.
 *
 * @package Papi
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Try convert to string if is possible else return empty string.
 *
 * @param mixed $obj
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_convert_to_string( $obj ) {
	if ( $obj === true ) {
		return 'true';
	}

	if ( $obj === false ) {
		return 'false';
	}

	if ( ! is_array( $obj ) && ( ( ! is_object( $obj ) && settype( $obj, 'string' ) !== false ) || ( is_object( $obj ) && method_exists( $obj, '__toString' ) ) ) ) {
		return (string) $obj;
	}

	return '';
}

/**
 * Check if current is allowed the given capabilities.
 *
 * @param array $capabilities
 *
 * @since 1.0.0
 *
 * @return bool
 */

function papi_current_user_is_allowed( $capabilities = array() ) {
	$capabilities = papi_to_array( $capabilities );

	foreach ( papi_to_array( $capabilities ) as $capability ) {
		if ( ! current_user_can( $capability ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Papi escape html.
 *
 * @param mixed $obj
 * @param array $keys
 *
 * @since 1.2.0
 *
 * @return mixed
 */

function papi_esc_html( $obj, $keys = array() ) {
	$object = is_object( $obj );

	if ( $object ) {
		$obj = (array) $obj;
	}

	if ( is_array( $obj ) ) {
		foreach ( $obj as $key => $value ) {
			if ( in_array( $key, $keys ) ) {
				continue;
			}

			if ( is_string( $key ) ) {
				unset( $obj[$key] );
				$key = papi_esc_html( $key );
			}

			if ( is_string( $value ) || is_object( $value ) || is_array( $obj ) ) {
				$obj[$key] = papi_esc_html( $value, $keys );
			}
		}

		if ( $object ) {
			return (object) $obj;
		}

		return $obj;
	} else if ( is_string( $obj ) ) {
		return esc_html( $obj );
	} else {
		return $obj;
	}
}

/**
 * Add a underscore at the start of the string.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_f( $str = '' ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	if ( strpos( $str, '_' ) === 0 ) {
		return $str;
	}

	return '_' . $str;
}

/**
 * Add two underscores at the start of the string.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_ff( $str = '' ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	if ( substr( $str, 0, 1 ) === '_' ) {
		if ( substr( $str, 1, 1 ) === '_' ) {
			return $str;
		}

		return '_' . $str;
	}

	return '__' . $str;
}

/**
 * Dashify the given string.
 * Replacing whitespace and underscore with a dash.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_dashify( $str ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	return str_replace( ' ', '-', str_replace( '_', '-', $str ) );
}

/**
 * Get namespace name and/or class name from page type file.
 *
 * @param string $file
 *
 * @since 1.0.0
 *
 * @return string|null
 */

function papi_get_class_name( $file ) {
	if ( ! is_string( $file ) ) {
		return '';
	}

	$content         = file_get_contents( $file );
	$tokens          = token_get_all( $content );
	$class_name      = '';
	$namespace_name  = '';
	$i               = 0;
	$len             = count( $tokens );

	for ( ; $i < $len;$i++ ) {
		if ( $tokens[$i][0] === T_NAMESPACE ) {
			for ( $j = $i + 1; $j < $len; $j++ ) {
				if ( $tokens[$j][0] === T_STRING ) {
					 $namespace_name .= '\\' . $tokens[$j][1];
				} else if ( $tokens[$j] === '{' || $tokens[$j] === ';' ) {
					 break;
				}
			}
		}

		if ( $tokens[$i][0] === T_CLASS ) {
			for ( $j = $i + 1; $j < $len; $j++ ) {
				if ( $tokens[$j] === '{' ) {
					$class_name = $tokens[$i + 2][1];
				}
			}
		}
	}

	if ( empty( $namespace_name ) ) {
		return $class_name;
	}

	return $namespace_name . '\\' . $class_name;
}

/**
 * Get only objects from $arr.
 *
 * @param array $arr
 *
 * @since 1.1.0
 *
 * @return array
 */

function papi_get_only_objects( $arr ) {
	return array_filter( papi_to_array( $arr ), function ( $item ) {
		return is_object( $item );
	} );
}

/**
 * Get value from $_GET or $_POST with the given key.
 *
 * @param string $key
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_get_or_post( $key ) {
	if ( ! is_string( $key ) ) {
		return;
	}

	if ( $value = papi_get_qs( $key ) ) {
		return $value;
	}

	if ( $value = papi_get_sanitized_post( $key ) ) {
		return $value;
	}
}

/**
 * Get query string if it exists and is not empty.
 *
 * @param array|string $qs
 *
 * @since 1.0.0
 *
 * @return array|string
 */

function papi_get_qs( $qs, $keep_keys = false ) {
	if ( ! is_string( $qs ) && ! is_array( $qs ) ) {
		return;
	}

	if ( is_array( $qs ) ) {
		if ( $keep_keys ) {
			$result = array();

			foreach ( $qs as $key ) {
				$value = papi_get_qs( $key );

				if ( ! papi_is_empty( $value ) ) {
					$result[$key] = $value;
				}
			}

			return $result;
		} else {
			return array_map( 'papi_get_qs', $qs );
		}
	}

	if ( isset( $_GET[ $qs ] ) && ! empty( $_GET[ $qs ] ) ) {
		$value = sanitize_text_field( $_GET[ $qs ] );

		if ( $value === 'false' ) {
			$value = false;
		}

		if ( $value === 'true' ) {
			$value = true;
		}

		return $value;
	}
}

/**
 * Get sanitized value from global `$_POST`.
 *
 * @param string $key
 *
 * @since 1.3.0
 *
 * @return string
 */

function papi_get_sanitized_post( $key ) {
	if ( ! isset( $_POST[$key] ) ) {
		return;
	}

	return sanitize_text_field( $_POST[$key] );
}

/**
 * Check if $obj is set and if not return null or default.
 *
 * @param mixed $obj The value to check if it is empty or not.
 * @param mixed $default The value to return if var is not set.
 *
 * @since 1.0.0
 *
 * @return mixed
 */

function papi_h( $obj, $default = null ) {
	return empty( $obj ) ? $default : $obj;
}

/**
 * Get a php friendly name.
 *
 * @param string $name
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_html_name( $name ) {
	if ( ! is_string( $name ) ) {
		return '';
	}

	if ( ! preg_match( '/^\_\_papi|^\_papi/', $name ) ) {
		$name = papify( $name );

		if ( ! preg_match( '/\[.*\]/', $name ) ) {
			$name = papi_slugify( $name );
		}

		return papi_underscorify( $name );
	}

	return $name;
}

/**
 * Check if $obj is empty or not.
 * Values like "0", 0 and false
 * should not return true.
 *
 * @param mixed $obj
 *
 * @since 1.0.3
 *
 * @return bool
 */

function papi_is_empty( $obj ) {
	if ( is_string( $obj ) ) {
		return empty( $obj ) && ! is_numeric( $obj );
	}

	if ( is_bool( $obj ) || is_numeric( $obj ) ) {
		return false;
	}

	return empty( $obj );
}

/**
 * Check if string has a extension.
 *
 * @param string $str
 * @param string $ext
 *
 * @since 1.0.0
 *
 * @return bool
 */

function papi_is_ext( $str, $ext ) {
	if ( is_string( $str ) ) {
		$arr = explode( '.', $str );
		return end( $arr ) === $ext;
	}

	return false;
}

/**
 * Replace '\n' with '<br />'.
 *
 * @param string $str
 *
 * @since 1.2.0
 *
 * @return string
 */

function papi_nl2br( $str ) {
	return str_replace( '\n', '<br />', nl2br( $str ) );
}

/**
 * Remove `papi-` or `papi_` from the given string.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_remove_papi( $str ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	return str_replace( 'papi-', '', str_replace( 'papi_', '', $str ) );
}

/**
 * Remove trailing dobule quote.
 * PHP's $_POST object adds this automatic.
 *
 * @param string $str The string to check.
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_remove_trailing_quotes( $str ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	return str_replace( "\'", "'", str_replace( '\"', '"', $str ) );
}

/**
 * Santize data.
 *
 * @param mixed $obj
 * @since 1.3.0
 *
 * @return mixed
 */

function papi_santize_data( $obj ) {
	if ( is_array( $obj ) ) {
		foreach ( $obj as $k => $v ) {
			if ( is_string( $v ) ) {
				$obj[ $k ] = papi_santize_data( $v );
			}
		}
	} else if ( is_string( $obj ) ) {
		$obj = papi_remove_trailing_quotes( $obj );
	}

	return $obj;
}

/**
 * Sort array based on given key and numeric value.
 *
 * @param array $array
 * @param string $key
 *
 * @since 1.0.0
 *
 * @return array
 */

function papi_sort_order( $array, $key = 'sort_order' ) {
	if ( empty( $array ) || ! is_array( $array ) && ! is_object( $array ) ) {
		return array();
	}

	if ( is_object( $array ) ) {
		$array = papi_to_array( $array );
	}

	$sorter = array();

	foreach ( $array as $k => $value ) {
		if ( is_object( $value ) ) {
			if ( isset( $value->$key ) ) {
				$sorter[ $k ] = $value->$key;
			} else if ( isset( $value->options ) && isset( $value->options->$key ) ) {
				$sorter[ $k ] = $value->options->$key;
			}
		} else if ( is_array( $value ) && isset ( $value[ $key ] ) ) {
			$sorter[ $k ] = $value[ $key ];
		}
	}

	$i = 0;
	$default_sort = papi_filter_settings_sort_order();

	foreach ( $sorter as $k => $v ) {
		if ( $default_sort === $v ) {
			$sorter[$k] = $v - $i;
			$i++;
		}
	}

	asort( $sorter, SORT_NUMERIC );

	$result = array();
	$rest   = array();

	foreach ( $sorter as $k => $v ) {
		$value = $array[ $k ];
		if ( ( is_object( $value ) && ( ! isset( $value->options ) && ! isset( $value->options->$key ) || ! isset( $value->$key ) ) ) || ( is_array( $value ) && ! isset( $value[ $key ] ) ) ) {
			$rest[] = $value;
		} else {
			$result[ $k ] = $array[ $k ];
		}
	}

	$result = array_values( $result );

	foreach ( $rest as $key => $value ) {
		$result[] = $value;
	}

	return $result;
}

/**
 * Slugify the given string.
 *
 * @param string $str
 * @param array $replace
 * @param string $delimiter
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_slugify( $str, $replace = array(), $delimiter = '-' ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	setlocale( LC_ALL, 'en_US.UTF8' );

	if ( ! empty( $replace ) ) {
		$str = str_replace( (array) $replace, ' ', $str );
	}

	$clean = iconv( 'UTF-8', 'ASCII//TRANSLIT', $str );
	$clean = preg_replace( '/[^a-zA-Z0-9\/_|+ -]/', '', $clean );
	$clean = strtolower( trim( $clean, '-' ) );
	$clean = preg_replace( '/[\/_|+ -]+/', $delimiter, $clean );

	return trim( $clean );
}

/**
 * Get a array of $obj.
 *
 * @param mixed $obj
 *
 * @since 1.0.0
 *
 * @return array
 */

function papi_to_array( $obj ) {
	if ( ! is_array( $obj ) ) {
		$obj = array( $obj );
	}

	return $obj;
}

/**
 * Underscorify the given string.
 * Replacing whitespace and dash with a underscore.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function papi_underscorify( $str ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	return str_replace( ' ', '_', str_replace( '-', '_', $str ) );
}

/**
 * Add `papi_` to the given string ad the start of the string.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function papify( $str = '' ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	if ( ! preg_match( '/^\_\_papi|^\_papi|^papi\_/', $str ) ) {
		return 'papi_' . $str;
	}

	return $str;
}
