<?php

/**
 * Papi utilities functions.
 *
 * @package Papi
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Try convert to string if is possible else return empty string.
 *
 * @param mixed $obj
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
 * @param array|string $capabilities
 *
 * @return bool
 */
function papi_current_user_is_allowed( $capabilities = [] ) {
	if ( ! is_array( $capabilities ) && ! is_string( $capabilities ) || empty( $capabilities ) ) {
		return true;
	}

	$capabilities = papi_to_array( $capabilities );

	foreach ( papi_to_array( $capabilities ) as $capability ) {
		if ( ! current_user_can( $capability ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Check if Papi is doing a AJAX request or not.
 *
 * @return bool
 */
function papi_doing_ajax() {
	return defined( 'DOING_PAPI_AJAX' ) && DOING_PAPI_AJAX;
}

/**
 * Papi escape html.
 *
 * @param mixed $obj
 * @param array $keys
 *
 * @return mixed
 */
function papi_esc_html( $obj, $keys = [] ) {
	$object = is_object( $obj ) && get_class( $obj ) === 'stdClass';

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
 * Dashify the given string.
 * Replacing whitespace and underscore with a dash.
 *
 * @param string $str
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
 * Add underscores at the start of the string.
 *
 * @param string $str
 * @param int $len
 *
 * @return string
 */
function papi_f( $str = '', $len = 1 ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	$prefix = '';

	for ( $i = 0; $i < $len; $i++ ) {
		$prefix .= '_';
	}

	if ( strpos( $str, $prefix ) === 0 ) {
		return $str;
	}

	return $prefix . preg_replace( '/^\_/', '', $str );
}

/**
 * Get Papi cache key.
 *
 * @param string $key
 * @param mixed $suffix
 *
 * @return string
 */
function papi_get_cache_key( $key, $suffix ) {
	if ( ! is_string( $key ) ) {
		return '';
	}

	$key    = papify( $key );
	$suffix = papi_convert_to_string( $suffix );
	$suffix = papi_html_name( $suffix );
	$suffix = papi_remove_papi( $suffix );
	return sprintf( '%s_%s', $key, $suffix );
}

/**
 * Get namespace name and/or class name from page type file.
 *
 * @param string $file
 *
 * @return null|string
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
 * @return array
 */
function papi_get_only_objects( array $arr ) {
	return array_filter( papi_to_array( $arr ), function ( $item ) {
		return is_object( $item );
	} );
}

/**
 * Get value from $_GET or $_POST with the given key.
 *
 * @param string $key
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
 * @return array|string
 */
function papi_get_qs( $qs, $keep_keys = false ) {
	if ( ! is_string( $qs ) && ! is_array( $qs ) ) {
		return;
	}

	if ( is_array( $qs ) ) {
		if ( $keep_keys ) {
			$results = [];

			foreach ( $qs as $key ) {
				$value = papi_get_qs( $key );

				if ( ! papi_is_empty( $value ) ) {
					$results[$key] = $value;
				}
			}

			return $results;
		} else {
			return array_map( 'papi_get_qs', $qs );
		}
	}

	if ( isset( $_GET[$qs] ) && ! empty( $_GET[$qs] ) ) {
		$value = $_GET[$qs];

		if ( is_string( $value ) ) {
			$value = sanitize_text_field( $value );
		}

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
 * @return string
 */
function papi_get_sanitized_post( $key ) {
	if ( ! isset( $_POST[$key] ) ) {
		return;
	}

	return sanitize_text_field( $_POST[$key] );
}

/**
 * Get a php friendly name.
 *
 * @param string $name
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
 * Get html tag from tag name and array of attributes.
 *
 * @param string $tag
 * @param array $attr
 *
 * @return string
 */
function papi_html_tag( $tag, $attr = [] ) {
	$attributes = [];
	$text       = [];

	if ( ! is_array( $attr ) ) {
		$attr = [$attr];
	}

	foreach ( $attr as $key => $value ) {
		if ( is_numeric( $key ) ) {
			if ( is_array( $value ) ) {
				$text[] = implode( ' ', $value );
			} else {
				$text[] = $value;
			}

			continue;
		}

		if ( is_array( $value ) || is_object( $value ) ) {
			$value = json_encode( $value );
		} else if ( is_bool( $value ) ) {
			$value = $value ? 'true' : 'false';
		} else if ( is_string( $value ) ) {
			$value = trim( $value );
		}

		if ( is_null( $value ) ) {
			continue;
		}

		$attributes[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
	}

	if ( papi_is_empty( $text ) ) {
		$end = '/>';
	} else {
		$end = sprintf( '>%s</%s>', implode( ' ', $text ), $tag );
	}

	if ( ! empty( $attributes ) ) {
		$attributes = ' ' . implode( ' ', $attributes );
	} else {
		$attributes = '';
	}

	return sprintf( '<%s%s%s', $tag, $attributes, $end );
}

/**
 * Check if the given object is empty or not.
 * Values like "0", 0 and false should not return true.
 *
 * @param mixed $obj
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
 * Check which http method it is.
 *
 * @param string $method
 *
 * @return bool
 */
function papi_is_metod( $method ) {
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || ! is_string( $method ) ) {
		return false;
	}

	return $_SERVER ['REQUEST_METHOD'] === strtoupper( $method );
}

/**
 * Replace '\n' with '<br />'.
 *
 * @param string $str
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
 * @return string
 */
function papi_remove_trailing_quotes( $str ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	return str_replace( "\'", "'", str_replace( '\"', '"', $str ) );
}

/**
 * Render html tag from tag name and array of attributes.
 *
 * @param string $tag
 * @param array $attr
 *
 * @return string
 */
function papi_render_html_tag( $tag, $attr = [] ) {
	echo papi_html_tag( $tag, $attr );
}

/**
 * Santize data.
 *
 * @param mixed $obj
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
 * @return array
 */
function papi_sort_order( $array, $key = 'sort_order' ) {
	if ( empty( $array ) || ! is_array( $array ) && ! is_object( $array ) ) {
		return [];
	}

	if ( is_object( $array ) ) {
		$array = papi_to_array( $array );
	}

	$sorter = [];

	foreach ( $array as $k => $value ) {
		if ( is_object( $value ) ) {
			if ( isset( $value->$key ) ) {
				$sorter[$k] = $value->$key;
			} else if ( isset( $value->options->$key ) ) {
				$sorter[$k] = $value->options->$key;
			}
		} else if ( is_array( $value ) && isset( $value[$key] ) ) {
			$sorter[$k] = $value[$key];
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

	$result = [];
	$rest   = [];

	foreach ( $sorter as $k => $v ) {
		$value = $array[ $k ];

		if ( ( is_object( $value ) && ( ! isset( $value->options ) && ! isset( $value->options->$key ) || ! isset( $value->$key ) ) ) || ( is_array( $value ) && ! isset( $value[$key] ) ) ) {
			$rest[] = $value;
		} else {
			$result[$k] = $array[$k];
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
 * @return string
 */
function papi_slugify( $str, $replace = [], $delimiter = '-' ) {
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
 * @return array
 */
function papi_to_array( $obj ) {
	if ( ! is_array( $obj ) ) {
		$obj = [$obj];
	}

	return $obj;
}

/**
 * Underscorify the given string.
 * Replacing whitespace and dash with a underscore.
 *
 * @param string $str
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
 * @return string
 */
function papify( $str = '' ) {
	if ( ! is_string( $str ) ) {
		return '';
	}

	if ( ! preg_match( '/^\_\_papi|^\_papi|^papi\_/', $str ) ) {
		if ( ! empty( $str ) && $str[0] === '_' ) {
			return 'papi' . $str;
		}

		return 'papi_' . $str;
	}

	return $str;
}
