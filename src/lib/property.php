<?php

/**
 * Delete property value from database.
 * If it's on a option page it will fetch the value from the
 * option table instead of the postmeta table.
 *
 * @param int    $post_id
 * @param string $slug
 * @param string $type
 */
function papi_delete_property_meta_value( $post_id, $slug, $type = 'post' ) {
	if ( $type === Papi_Core_Page::TYPE_OPTION || papi_is_option_page() ) {
		return delete_option( papi_remove_papi( $slug ) );
	}

	return delete_post_meta( $post_id, papi_remove_papi( $slug ) );
}

/**
 * Convert array of slugs to array with arrays in.
 *
 * @param  array  $values
 * @param  string $slug
 *
 * @return array
 */
function papi_from_property_array_slugs( array $values, $slug ) {
	$results = [];

	if ( empty( $values ) ) {
		return $results;
	}

	for ( $i = 0; $i < $values[$slug]; $i++ ) {
		$item      = [];
		$item_slug = $slug . '_' . $i . '_';
		$keys      = preg_grep( '/' . preg_quote( $item_slug ). '/' , array_keys( $values ) );

		foreach ( $keys as $key ) {
			$arr_key = str_replace( $item_slug, '', $key );
			$item[$arr_key] = $values[$key];
		}

		$results[] = $item;
	}

	return $results;
}

/**
 * Check if the given value is a instance of a property or not.
 *
 * @param  mixed $value
 *
 * @return bool
 */
function papi_is_property( $value ) {
	return $value instanceof Papi_Property;
}

/**
 * Get box property.
 *
 * @param  array $properties
 *
 * @return array
 */
function papi_get_box_property( array $properties ) {
	$box_property = array_filter( $properties, function ( $property ) {
		return ! is_object( $property );
	} );

	if ( ! empty( $box_property ) && ! isset( $box_property[0] ) && ! isset( $box_property[0]['tab'] ) ) {
		$property = papi_property( $properties );

		if ( ! $property->disabled() ) {
			$property->_box_property = true;
			$properties = [$property];
		}
	}

	return $properties;
}

/**
 * Get options and properties.
 *
 * @param  mixed $file_or_options
 * @param  array $properties
 * @param  bool  $is_box
 *
 * @return array
 */
function papi_get_options_and_properties( $file_or_options = [], $properties = [], $is_box = true ) {
	$options = [];

	if ( is_array( $file_or_options ) ) {
		if ( empty( $properties ) && $is_box ) {
			// Check if we have a title or not.
			if ( isset( $file_or_options['title'] ) ) {
				$options['title'] = $file_or_options['title'];
			} else if ( isset( $file_or_options[0]->title ) ) {
				$options['title'] = $file_or_options[0]->title;
				if ( $file_or_options[0]->sidebar === false && $file_or_options[0]->required ) {
					$options['_required'] = true;
				}
			} else if ( isset( $file_or_options[0]->options ) && isset( $file_or_options[0]->options['title'] ) ) {
				$options['title'] = $file_or_options[0]->options['title'];
			} else {
				$options['title'] = '';
			}
			$properties  = $file_or_options;
		} else {
			$options = array_merge( $options, $file_or_options );

			if ( ! $is_box ) {
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
		if ( preg_match( '/\.php$/', $file_or_options ) === 1 ) {
			$values = $properties;
			$template = papi_template( $file_or_options, $values );

			// Create the property array from existing property array or a new.
			$properties = [];
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

	return [$options, $properties];
}

/**
 * Get property class name.
 *
 * @param  string $type
 *
 * @return string
 */
function papi_get_property_class_name( $type ) {
	if ( ! is_string( $type ) || empty( $type ) ) {
		return;
	}

	return 'Papi_Property_' . ucfirst( preg_replace( '/^Property/', '', $type ) );
}

/**
 * Get property value from database.
 * If it's on a option page it will fetch the value from the
 * option table instead of the postmeta table.
 *
 * @param int    $post_id
 * @param string $slug
 * @param string $type
 */
function papi_get_property_meta_value( $post_id, $slug, $type = 'post' ) {
	if ( $type === Papi_Core_Page::TYPE_OPTION || papi_is_option_page() ) {
		$value = get_option( $slug, null );
	} else {
		$value = get_post_meta( $post_id, $slug, true );
	}

	if ( papi_is_empty( $value ) ) {
		return;
	}

	return $value;
}

/**
 * Get property options.
 *
 * @param  array $options
 *
 * @return stdClass
 */
function papi_get_property_options( $options ) {
	if ( ! is_array( $options ) ) {
		if ( is_object( $options ) ) {
			return $options;
		}

		return;
	}

	$property = Papi_Property::create( $options );

	return $property->get_options();
}

/**
 * Get property type by the given type.
 *
 * @param  object|string $type
 *
 * @return null|Papi_Property
 */
function papi_get_property_type( $type ) {
	if ( papi_is_empty( $type ) ) {
		return;
	}

	return Papi_Property::factory( $type );
}

/**
 * Get the right key for a property type.
 *
 * @param  string $str
 *
 * @return string
 */
function papi_get_property_type_key( $str = '' ) {
	$suffix = '_property';

	if ( ! is_string( $str ) || strlen( $str ) === 0 ) {
		return $suffix;
	}

	$len = strlen( $str );

	if ( isset( $str[$len - 1] ) && $str[$len - 1] === ']' ) {
		$str = substr( $str, 0, $len - 1 );
		return papi_get_property_type_key( $str ) . ']';
	}

	return papi_remove_papi( $str . $suffix );
}

/**
 * Get the right key for a property type with a underscore as the first character.
 *
 * @param  string $str
 *
 * @return string
 */
function papi_get_property_type_key_f( $str ) {
	return papi_f( papi_get_property_type_key( $str ) );
}

/**
 * Check if it's ends with '_property'.
 *
 * @param  string $str
 *
 * @return bool
 */
function papi_is_property_type_key( $str = '' ) {
	$pattern = '_property';
	$pattern = str_replace( '_', '\_', $pattern );
	$pattern = str_replace( '-', '\-', $pattern );
	$pattern = '/' . $pattern . '$/';

	return preg_match( $pattern, $str ) === 1;
}

/**
 * Create a new property array or rendering a template property file.
 *
 * @param  mixed $file_or_options
 * @param  array $values
 *
 * @return null|Papi_Property
 */
function papi_property( $file_or_options, $values = [] ) {
	if ( papi_is_empty( $file_or_options ) ) {
		return;
	}

	if ( is_array( $file_or_options ) ) {
		$file_or_options = papi_get_property_options( $file_or_options );
	}

	if ( is_string( $file_or_options ) && is_array( $values ) ) {
		$file_or_options = papi_template( $file_or_options, $values );
	}

	if ( is_object( $file_or_options ) ) {
		return papi_get_property_type( $file_or_options );
	}
}

/**
 * Render the given property.
 *
 * @param Papi_Property $property
 */
function papi_render_property( $property ) {
	$property = Papi_Property::factory( $property );

	if ( is_null( $property ) ) {
		return;
	}

	$property->render();
}

/**
 * Render properties the right way.
 *
 * @param array $properties
 */
function papi_render_properties( array $properties ) {
	if ( empty( $properties ) ) {
		return;
	}

	// If it's a tab the tabs class will
	// handle the rendering of the properties.
	if ( isset( $properties[0]->tab ) && $properties[0]->tab ) {
		new Papi_Admin_Meta_Box_Tabs( $properties );
	} else {
		?>

		<table class="papi-table">
			<tbody>
			<?php
			foreach ( $properties as $property ) {
				papi_render_property( $property );
			}
			?>
			</tbody>
		</table>

	<?php
	}
}

/**
 * Get require text for property.
 *
 * @param  Papi_Property $property
 *
 * @return string
 */
function papi_require_text( $property ) {
	if ( ! is_object( $property ) || ! $property->required ) {
		return '';
	}

	return esc_html__( '(required field)', 'papi' );
}

/**
 * Get require tag for property.
 *
 * @param  Papi_Property $property
 * @param  bool $text
 *
 * @return string
 */
function papi_required_html( $property, $text = false ) {
	if ( ! is_object( $property ) || ! $property->required ) {
		return '';
	}

	return ' <span class="papi-rq" data-property-name="' . $property->title . '" data-property-id="' . $property->slug . '">' . ( $text ? papi_require_text( $property ) : '*' ) . '</span>';
}

/**
 * Populate properties array.
 *
 * @param  array|object $properties
 *
 * @return array
 */
function papi_populate_properties( $properties ) {
	if ( ! is_array( $properties ) && ! is_object( $properties ) || empty( $properties ) ) {
		return [];
	}

	if ( is_object( $properties )  ) {
		return [$properties];
	}

	$results = [];

	// Get the box property (when you only put a array in the box method) if it exists.
	$properties = papi_get_box_property( $properties );

	// Convert all non property objects to property objects.
	$properties = array_map( function ( $property ) {
		if ( ! is_object( $property ) && is_array( $property ) && ! isset( $property['tab'] ) ) {
			return papi_get_property_options( $property );
		}

		return $property;
	}, $properties );

	// Fix so the properties array will have the right order.
	$properties = array_reverse( $properties );

	foreach ( $properties as $property ) {
		if ( isset( $property->tab ) && $property->tab ) {
			$results[] = $property;
			continue;
		}

		$results[] = $property;
	}

	if ( empty( $results ) || ( isset( $results[0]->tab ) && $results[0]->tab ) ) {
		return $results;
	}

	return papi_sort_order( $results );
}

/**
 * Update property values on the post with the given post id
 * or update property values on the option page.
 *
 * @param  array $meta
 *
 * @return bool
 */
function papi_update_property_meta_value( array $meta = [] ) {
	$meta       = (object) $meta;
	$option     = papi_is_option_page();
	$save_value = true;

	foreach ( papi_to_array( $meta->value ) as $key => $value ) {
		if ( is_string( $key ) ) {
			$save_value = false;
			break;
		}
	}

	if ( ! isset( $meta->post_id ) ) {
		$meta->post_id = 0;
	}

	if ( ! $save_value && is_array( $meta->value ) ) {
		$meta->value = [$meta->value];
	}

	if ( papi_is_empty( $meta->value ) ) {
		papi_cache_delete( $meta->slug, $meta->post_id );

		if ( $option ) {
			return delete_option( papi_remove_papi( $meta->slug ) );
		} else {
			return delete_post_meta( $meta->post_id, papi_remove_papi( $meta->slug ) );
		}
	}

	$result = true;

	foreach ( papi_to_array( $meta->value ) as $key => $value ) {
		papi_cache_delete( $meta->slug, $meta->post_id );

		if ( ! is_array( $value ) ) {
			if ( $save_value ) {
				$value = $meta->value;
			}

			if ( $option ) {
				$out = update_option( papi_remove_papi( $meta->slug ), $value );
				$result = $out ? $result : $out;
			} else {
				$out = update_post_meta( $meta->post_id, papi_remove_papi( $meta->slug ), $value );
				$result = $out ? $result : $out;
			}

			continue;
		}

		foreach ( $value as $child_key => $child_value ) {
			if ( papi_is_empty( $child_value ) ) {
				if ( $option ) {
					delete_option( papi_remove_papi( $child_key ) );
				} else {
					delete_post_meta( $meta->post_id, papi_remove_papi( $child_key ) );
				}
			} else {
				if ( $option ) {
					update_option( papi_remove_papi( $child_key ), $child_value );
				} else {
					update_post_meta( $meta->post_id, papi_remove_papi( $child_key ), $child_value );
				}
			}
		}
	}

	return $result;
}

/**
 * Convert array of arrays to array of slugs.
 * The given slug will match a key with the number of properties.
 *
 * @param  array  $value
 * @param  string $slug
 *
 * @return array
 */
function papi_to_property_array_slugs( array $value, $slug ) {
	$results = [];
	$counter = [];

	foreach ( $value as $index => $arr ) {

		if ( ! is_array( $arr ) ) {
			continue;
		}

		$counter[] = $arr;

		foreach ( $arr as $key => $val ) {

			if ( ! is_string( $key ) || empty( $key ) ) {
				continue;
			}

			if ( $key[0] !== '_' ) {
				$key = '_' . $key;
			}

			$item_slug = $slug . '_' . $index . $key;

			if ( papi_is_property_type_key( $item_slug ) ) {
				$item_slug = papi_f( $item_slug );
			}

			$results[$item_slug] = $val;
		}
	}

	$results[$slug] = count( $counter );

	return $results;
}
