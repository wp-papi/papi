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
 * Convert array of slugs to array with arrays in.
 *
 * @param array $value
 * @param string $slug
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_from_property_array_slugs( $value, $slug ) {
	$result = array();

	if ( empty( $value ) ) {
		return array();
	}

	for ( $i = 0; $i < $value[$slug]; $i++ ) {
		$item      = array();
		$item_slug = $slug . '_' . $i . '_';
		$keys      = preg_grep( '/' . preg_quote( $item_slug ). '/' , array_keys( $value ) );

		foreach ( $keys as $key ) {
			$arr_key = str_replace( $item_slug, '', $key );
			$item[$arr_key] = $value[$key];
		}

		$result[] = $item;
	}

	return $result;
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

function _papi_get_box_property( $properties ) {
	$box_property = array_filter( $properties, function ( $property ) {
		return ! is_object( $property );
	} );

	if ( ! empty( $box_property ) && ! isset( $box_property[0] ) && ! isset( $box_property[0]['tab'] ) ) {
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

function _papi_get_options_and_properties( $file_or_options = array(), $properties = array(), $is_box = true ) {
	$options = array();

	if ( is_array( $file_or_options ) ) {
		if ( empty( $properties ) && $is_box ) {
			// Check if we have a title or not.
			if ( isset( $file_or_options['title'] ) ) {
				$options['title'] = $file_or_options['title'];
			} else if ( isset( $file_or_options[0]->title ) ) {
				$options['title'] = $file_or_options[0]->title;
			} else {
				$options['title'] = '';
			}
			$properties 	  = $file_or_options;
		} else {
			$options = array_merge( $options, $file_or_options );

			if ( !$is_box ) {
				// Add all non string keys to the properties array
				foreach ( $options as $key => $value ) {
					if ( ! is_string( $key ) ) {
						$properties[] = $value;
						unset( $options[$key] );
					}
				}
			}
		}
	} else if ( is_string( $file_or_options ) ) {
		// If it's a template we need to load it the right way
		// and add all properties the right way.

		if ( _papi_is_ext( $file_or_options, 'php' ) ) {
			$values = $properties;
			$template = _papi_template( $file_or_options, $values );

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

	return array( $options, $properties );
}

/**
 * Get property type default settings
 *
 * @param string $type
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_get_property_default_settings( $type ) {
	$property_type = _papi_get_property_type( $type );

	if ( is_null( $property_type ) || ! method_exists( $property_type, 'get_default_settings' ) ) {
		return array();
	}

	return $property_type->get_default_settings();
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
		if ( is_object( $options ) ) {
			return $options;
		} else {
			return null;
		}
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
		'sort_order'   => _papi_get_option( 'sort_order', 1000 ),
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

	// Get the default settings for the property and merge them with the given settings.
	$options->settings = array_merge( _papi_get_property_default_settings( $options->type ), $options->settings );
	$options->settings = (object)$options->settings;

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
 * Get property class name.
 *
 * @param string $type
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_property_class_name( $type ) {
	$type = _papi_get_property_short_type( $type );

	if ( empty( $type ) ) {
		return null;
	}

	return 'Papi_Property_' . ucfirst( $type );
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
	if ( ! is_string( $type ) ) {
		return null;
	}

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
	$suffix = '_property';

	if ( ! is_string( $str ) ) {
		return $suffix;
	}

	return _papi_remove_papi( $str . $suffix );
}

/**
 * Get the right key for a property type with a underscore as the first character.
 *
 * @param string $str
 *
 * @since 1.0.0
 *
 * @return string
 */

function _papi_get_property_type_key_f( $str ) {
	return _papi_f( _papi_get_property_type_key( $str ) );
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

	return preg_match( $pattern, $str ) === 1;
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

	if ( is_array( $properties ) && isset( $properties[0]->tab ) && $properties[0]->tab ) {
		new Papi_Admin_Meta_Box_Tabs( $properties );
	} else {
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
 * @param array|object $properties
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_populate_properties( $properties ) {
	// If $properties is a object we can just return it in a array.
	if ( is_object( $properties )  ) {
		return array( $properties );
	}

	$result = array();

	// Get the box property (when you only put a array in the box method) if it exists.
	$properties = _papi_get_box_property( $properties );

	// Convert all non property objects to property objects.
	$properties = array_map( function ( $property ) {
		if ( ! is_object( $property ) && is_array( $property ) && ! isset( $property['tab'] ) ) {
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
			if ( isset( $property->tab ) && $property->tab ) {
				$result[] = $property;
				continue;
			}

			$result[] = $property;
		}
	}

	if ( empty( $result ) ) {
		return array();
	}

	if ( isset( $result[0]->tab ) && $result[0]->tab ) {
		return $result;
	}

	return _papi_sort_order( $result );
}

/**
 * Update property values on the post with the given post id.
 *
 * @param int $post_id
 * @param string $key
 * @param array $property
 *
 * @since 1.0.0
 */

function _papi_property_update_meta( $meta ) {
	$meta = (object)$meta;

	if ( empty( $meta->value ) || empty( $meta->type ) ) {
		return false;
	}

	$save_value = true;

	foreach ( _papi_to_array( $meta->value ) as $key => $value ) {
		if ( is_string( $key ) ) {
			$save_value = false;
			break;
		}
	}

	if ( ! $save_value && is_array( $meta->value ) ) {
		$meta->value = array( $meta->value );
	}

	foreach ( _papi_to_array( $meta->value ) as $key => $value ) {

		if ( ! is_array( $value ) ) {

			if ( is_numeric( $key ) ) {
				$slug = _papi_remove_papi( $meta->slug );
			} else {
				$slug = $key;
			}

			if ( $save_value ) {
				$value = $meta->value;
			}

			update_post_meta( $meta->post_id, $slug, $value );

			continue;
		}

		foreach ( $value as $child_key => $child_value ) {
			update_post_meta( $meta->post_id, _papi_remove_papi( $child_key ), $child_value );
		}

	}

	update_post_meta( $meta->post_id, _papi_get_property_type_key_f( $meta->slug ), $meta->type );
}

/**
 * Convert array of arrays to array of slugs.
 * The given slug will match a key with the number of properties.
 *
 * @param array $value
 * @param string $slug
 *
 * @since 1.0.0
 *
 * @return array
 */

function _papi_to_property_array_slugs( $value, $slug ) {
	$result  = array();
	$counter = array();

	foreach ( $value as $index => $arr ) {

		if ( ! is_array( $arr ) ) {
			continue;
		}

		$counter[] = $arr;

		foreach ( $arr as $key => $val ) {
			$item_slug = $slug . '_' . $index . '_' . $key;

			if ( _papi_is_property_type_key( $item_slug ) ) {
				$item_slug = _papi_f( $item_slug );
			}

			$result[$item_slug] = $val;
		}
	}

	$result[$slug] = count( $counter );

	return $result;
}
