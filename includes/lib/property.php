<?php

/**
 * Papi property functions.
 *
 * @package Papi
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get box property.
 *
 * @param array $properties
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_get_box_property ($properties) {
	$box_property = array_filter( $properties, function ( $property ) {
		return ! is_object( $property );
	} );

	if ( ! empty( $box_property ) && ! isset($box_property[0]) ) {
		$property = _papi_get_property_options( $box_property );
		if ( ! $property->disabled ) {
			$properties = array( $property );
		}
	}

	return $properties;
}

/**
 * Returns only values in the array and removes `{x}_property` key and value.
 *
 * @param array $arr
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_get_only_property_values( $arr = array() ) {
	foreach ( $arr as $key => $value ) {
		if ( _papi_is_property_type_key( $key ) ) {
			unset( $arr[ $key ] );
		}
	}

	return $arr;
}

/**
 * Get options and properties.
 *
 * @param string|array $file_or_options
 * @param array $properties
 * @param bool $is_box
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_get_options_and_properties ($file_or_options = array(), $properties = array(), $is_box = true) {
	$options = array();

	if ( is_array( $file_or_options ) ) {
		if ( empty( $properties ) && $is_box ) {
			// The first parameter is the options array.
			$options['title'] = isset( $file_or_options['title'] ) ? $file_or_options['title'] : '';
			$properties 	  = $file_or_options;
		} else {
			$options = array_merge( $options, $file_or_options );
		}
	} else if ( is_string( $file_or_options ) ) {
		// If it's a template we need to load it the right way
		// and add all properties the right way.
		if ( _papi_is_ext( $file_or_options, 'php' ) ) {
			$values = $properties;
			$template = papi_template( $file_or_options, $values );

			// Create the property array from existing property array or a new.
			$properties = array();
			$options = $template;

			// Add all non string keys to the properties array
			foreach ( $options as $key => $value ) {
				if ( ! is_string( $key ) ) {
					$properties[] = $value;
					unset( $options[$key] );
				}
			}

		} else {
			// The first parameter is used as the title.
			$options['title'] = $file_or_options;
		}
	}

	return array($options, $properties);
}

/**
 * Get property options.
 *
 * @param array $options
 * @param bool $get_value
 *
 * @since 1.0.0
 *
 * @return object
 */

function _papi_get_property_options( $options, $get_value = true ) {

	if ( ! is_array( $options ) ) {
		return null;
	}

	$defaults = array(
		'capabilities' => array(),
		'default'      => '',
		'disabled'     => false,
		'instruction'  => '',
		'lang'         => '',
		'raw'          => false,
		'settings'     => array(),
		'sidebar'      => true,
		'slug'         => '',
		'sort_order'   => 100,
		'required'     => false,
		'title'        => '',
		'type'         => '',
		'value'        => ''
	);

	$options = array_merge( $defaults, $options );
	$options = (object) $options;

	// Capabilities should always be array.
	if ( ! is_array( $options->capabilities ) ) {
		$options->capabilities = array( $options->capabilities );
	}

	// Generate random slug if we don't have a title or slug.
	if ( empty( $options->title ) && empty( $options->slug ) ) {
		$options->slug = _papi_slugify( uniqid() );
	}

	// Generate slug from title.
	if ( empty( $options->slug ) ) {
		$options->slug = _papi_slugify( $options->title );
	}

	// Generate a vaild Papi meta name for slug.
	$options->slug = _papi_html_name( $options->slug );

	// Generate a valid Papi meta name for old slug.
	if ( ! empty( $options->old_slug ) ) {
		$options->old_slug = _papi_html_name( $options->old_slug );
	}

	// This fixes so you can use "Text" as type and hasn't to write "PropertyText".
	if ( ! preg_match( '/^Property/', $options->type ) ) {
		$options->type = 'Property' . ucfirst( strtolower( $options->type ) );
	}

	if ( empty( $options->value ) && $get_value ) {
		// Get meta value for the field
		$post_id        = _papi_get_post_id();
		$options->value = _papi_field( $post_id, $options->slug, null, true );
	}

	// Add default value if database value is empty.
	if ( empty( $options->value ) ) {
		$options->value = $options->default;
	}

	return $options;
}

/**
 * Get property short type.
 *
 * @param string $type
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_property_short_type( $type ) {
	return preg_replace( '/^property/', '', strtolower( $type ) );
}

/**
 * Get property type by the given type.
 *
 * @param string $type
 *
 * @since 1.0.0
 *
 * @return null|Papi_Property
 */

function _papi_get_property_type( $type ) {
	if ( is_object( $type ) && isset( $type->type ) && is_string( $type->type ) ) {
		$type = $type->type;
	}

	return Papi_Property::factory( $type );
}

/**
 * Get the right key for a property type.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_property_type_key( $str = '' ) {
	return $str . '_property';
}

/**
 * Check if it's ends with '_property'.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return integer
 */

function _papi_is_property_type_key( $str = '' ) {
	$pattern = '_property';
	$pattern = str_replace( '_', '\_', $pattern );
	$pattern = str_replace( '-', '\-', $pattern );
	$pattern = '/' . $pattern . '$/';

	return preg_match( $pattern, $str );
}

/**
 * Render a property the right way.
 *
 * @param object $property
 *
 * @since 1.0.0
 */

function _papi_render_property( $property ) {
	// Check so type isn't empty and capabilities on the property.
	if ( empty( $property->type ) || ! _papi_current_user_is_allowed( $property->capabilities ) ) {
		return;
	}

	$property_type = _papi_get_property_type( $property->type );

	if ( is_null( $property_type ) ) {
		return;
	}

	$property_type->set_options( $property );

	// Only render if it's the right language if the definition exist.
	if ( _papi_get_qs( 'lang' ) != null ) {
		$render = $property->lang === strtolower( _papi_get_qs( 'lang' ) );
	} else {
		$render = true;
	}

	// Render the property.
	if ( $render ) {
		$property_type->assets();
		$property_type->render();
		$property_type->hidden();
	}
}

/**
 * Render properties the right way.
 *
 * @param array $properties
 *
 * @since 1.0.0
 */

function _papi_render_properties( $properties ) {
	// Don't proceed without any properties
	if ( ! is_array( $properties ) || empty( $properties ) ) {
		return;
	}

	// If it's a tab the tabs class will
	// handle the rendering of the properties.

	if ( isset( $properties[0]->tab ) && $properties[0]->tab ) {
		new Papi_Admin_Meta_Box_Tabs( $properties );
	} else {
		// Sort properties based on `sort_order` value.
		$properties = _papi_sort_order( $properties );

		?>

		<table class="papi-table">
			<tbody>
			<?php
			foreach ( $properties as $property ) {
				_papi_render_property( $property );
			}
			?>
			</tbody>
		</table>

	<?php
	}
}

/**
 * Populate properties array.
 *
 * @param array $properties
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_populate_properties ($properties) {
	$result = array();

	// Get the box property (when you only put a array in the box method) if it exists.
	$properties = _papi_get_box_property( $properties );

	// Convert all non property objects to property objects.
	$properties = array_map( function ( $property ) {
		if ( !is_object( $property ) && is_array( $property ) ) {
			return _papi_get_property_options( $property );
		}

		return $property;
	}, $properties );

	// Fix so the properties array will have the right order.
	$properties = array_reverse( $properties );

	foreach ( $properties as $property ) {
		if ( is_array( $property ) ) {
			foreach ( $property as $p ) {
				if ( is_object( $p ) && ! $p->disabled ) {
					$result[] = $p;
				}
			}
		} else if ( is_object( $property ) ) {
			$result[] = $property;
		}
	}

	return $result;
}
