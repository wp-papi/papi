<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Page Type class.
 *
 * All page types in the WordPress theme will
 * extend this class.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Page_Type extends Papi_Page_Type_Meta {

	/**
	 * The array of meta boxes to register.
	 *
	 * @var array
	 */

	protected $boxes = [];

	/**
	 * Load all boxes even if we aren't on a post type.
	 *
	 * @var bool
	 */

	private $load_boxes = false;

	/**
	 * Array of post type supports to remove.
	 * By default remove `postcustom` which is the Custom fields metabox.
	 *
	 * @var array
	 */

	private $post_type_supports = ['custom-fields'];

	/**
	 * Remove meta boxes.
	 *
	 * @var array
	 */

	private $remove_meta_boxes = [];

	/**
	 * Add new meta box with properties.
	 *
	 * @param mixed $file_or_options
	 * @param array $properties
	 */

	protected function box( $file_or_options = [], $properties = [] ) {
		if ( ! is_string( $file_or_options ) && ! is_array( $file_or_options ) && ! is_object( $file_or_options ) ) {
			return;
		}

		list( $options, $properties ) = papi_get_options_and_properties( $file_or_options, $properties, true );

		$post_type = $this->get_post_type();

		// Check so we have a post the to add the box to.
		if ( ! $this->load_boxes && ( empty( $post_type ) || ! $this->has_post_type( $post_type ) ) ) {
			return;
		}

		// Add post type to the options array.
		$options['post_type'] = $post_type;

		if ( isset( $options['sort_order'] ) ) {
			$sort_order = intval( $options['sort_order'] );
		} else {
			$sort_order = papi_filter_settings_sort_order();
		}

		if ( is_callable( $properties ) ) {
			$properties = call_user_func( $properties );
		}

		// Check and convert all non properties objects to properties objects.
		$properties = $this->convert_properties( $properties );

		$options['title'] = papi_esc_html( isset( $options['title'] ) ? $options['title'] : '' );

		array_push( $this->boxes, [$options, $properties, 'sort_order' => $sort_order, 'title' => $options['title']] );
	}

	/**
	 * Convert properties to properties objects.
	 *
	 * @param array|object $properties
	 *
	 * @return array
	 */

	private function convert_properties( $properties ) {
		if ( is_array( $properties ) ) {
			if ( isset( $properties['type'] ) ) {
				$properties = [$properties];
			} else if ( isset( $properties[0]->tab ) && $properties[0]->tab ) {
				foreach ( $properties as $index => $items ) {
					$items->properties = array_map( function ( $property ) {
						return papi_property( $property );
					}, $items->properties );

					$properties[$index]->properties = $this->convert_child_properties( $items->properties );
				}

				return $properties;
			}
		}

		if ( $properties instanceof Papi_Property ) {
			$properties = [$properties];
		}

		if ( ! is_array( $properties ) ) {
			return;
		}

		$properties = array_map( 'papi_property', $properties );

		$properties = $this->convert_child_properties( $properties );

		return array_filter( $properties, function ( $property ) {
			return is_object( $property ) && isset( $property->type );
		} );
	}
	/**
	 * Fix child properties so you can skip `papi_property`
	 * it in for example `settings->items`.
	 *
	 * @param array $properties
	 *
	 * @return array
	 */

	private function convert_child_properties( $properties ) {
		for ( $i = 0, $l = count( $properties ); $i < $l; $i++ ) {
			if ( ! isset( $properties[$i]->settings ) ) {
				continue;
			}

			$arr = (array) $properties[$i]->settings;

			foreach ( $arr as $key => $value ) {
				if ( ! is_array( $value ) ) {
					continue;
				}

				$arr[$key] = $this->convert_items_array( $value );
			}

			$properties[$i]->settings = (object) $arr;
		}

		return $properties;
	}

	/**
	 * Convert all arrays that has a valid property type.
	 *
	 * @param array $items
	 *
	 * @return array
	 */

	private function convert_items_array( $items ) {
		for ( $j = 0, $k = count( $items ); $j < $k; $j++ ) {
			if ( ! isset( $items[$j] ) || ! is_array( $items[$j] ) ) {
				continue;
			}

			if ( ! isset( $items[$j]['type'] ) ) {
				foreach ( $items[$j] as $key => $value ) {
					if ( is_array( $items[$j][$key] ) ) {
						$items[$j][$key] = $this->convert_items_array( $items[$j][$key] );
					}
				}
				continue;
			}

			$type = papi_get_property_class_name( $items[$j]['type'] );

			if ( ! class_exists( $type ) ) {
				continue;
			}

			$items[$j] = papi_property( $items[$j] );
		}

		return $items;
	}

	/**
	 * Should the Page Type be displayed in WordPress admin or not?
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 */

	public function display( $post_type ) {
		return true;
	}

	/**
	 * Get boxes from the page type.
	 *
	 * @return array
	 */

	public function get_boxes() {
		if ( empty( $this->boxes ) && $this->load_boxes == false ) {
			if ( ! method_exists( $this, 'register' ) ) {
				return;
			}

			$this->load_boxes = true;

			$this->register();
		}

		return $this->boxes;
	}

	/**
	 * Get post type.
	 *
	 * @return string
	 */

	public function get_post_type() {
		return papi_get_post_type();
	}

	/**
	 * Get child property.
	 *
	 * @param array $items
	 * @param string $slug
	 *
	 * @return object
	 */

	protected function get_child_property( $items, $slug ) {
		$result = null;

		foreach ( $items as $property ) {
			if ( is_array( $property ) ) {
				$result = $this->get_child_property( $property, $slug );

				if ( is_object( $result ) ) {
					return papi_get_property_type( $result );
				}
			}

			if ( is_object( $property ) ) {
				if ( papi_remove_papi( $property->array_slug ) === $slug ) {
					return papi_get_property_type( $property );
				}

				if ( papi_remove_papi( $property->slug ) === $slug ) {
					return papi_get_property_type( $property );
				}
			}
		}

		return $result;
	}

	/**
	 * Get property from page type.
	 *
	 * @param string $slug
	 * @param string $child_slug
	 *
	 * @return object
	 */

	public function get_property( $slug, $child_slug = '' ) {
		$boxes = $this->get_boxes();

		if ( empty( $boxes ) ) {
			return;
		}

		foreach ( $boxes as $box ) {
			if ( ! is_array( $box[1] ) ) {
				continue;
			}

			foreach ( $box[1] as $property ) {
				$property = papi_get_property_type( $property );

				if ( $property instanceof Papi_Property === false ) {
					continue;
				}

				if ( papi_remove_papi( $property->slug ) === $slug ) {
					if ( empty( $child_slug ) ) {
						return $property;
					}

					$result = $this->get_child_property( $property->get_child_properties(), $child_slug );

					if ( is_object( $result ) ) {
						return papi_get_property_type( $result );
					}
				}
			}
		}
	}

	/**
	 * This function will setup all meta boxes.
	 */

	public function setup() {
		if ( ! method_exists( $this, 'register' ) ) {
			return;
		}

		// 1. Run the register method.
		$this->register();

		// 2. Remove post type support
		$this->remove_post_type_support();

		// 3. Load all boxes.
		$this->boxes = papi_sort_order( array_reverse( $this->boxes ) );

		foreach ( $this->boxes as $box ) {
			new Papi_Admin_Meta_Box( $box[0], $box[1] );
		}
	}

	/**
	 * Add new property to the page using array or rendering property template file.
	 *
	 * @param string|array $file_or_options
	 * @param array $values
	 *
	 * @return array
	 */

	protected function property( $file_or_options = [], $values = [] ) {
		return papi_property( $file_or_options, $values );
	}

	/**
	 * Remove post type support. Runs once, on page load.
	 *
	 * @param array $post_type_supports
	 */

	protected function remove( $post_type_supports = [] ) {
		$this->post_type_supports = array_merge( $this->post_type_supports, papi_to_array( $post_type_supports ) );
	}

	/**
	 * Remove post type support action.
	 */

	public function remove_post_type_support() {
		global $_wp_post_type_features;

		$post_type = $this->get_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		foreach ( $this->post_type_supports as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$key = $value;
				$value = '';
			}

			if ( isset( $_wp_post_type_features[$post_type] ) && isset( $_wp_post_type_features[$post_type][$key] ) ) {
				unset( $_wp_post_type_features[$post_type][$key] );
				continue;
			}

			// Add non post type support to remove meta boxes array.
			if ( empty( $value ) ) {
				$value = 'normal';
			}

			$this->remove_meta_boxes[] = [$key, $value];
		}

		add_action( 'add_meta_boxes', [$this, 'remove_meta_boxes'], 999 );
	}

	/**
	 * Remove meta boxes.
	 */

	public function remove_meta_boxes() {
		$post_type = $this->get_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		foreach ( $this->remove_meta_boxes as $item ) {
			remove_meta_box( $item[0], $post_type, $item[1] );
		}
	}

	/**
	 * Add a new tab.
	 *
	 * @param mixed $file_or_options
	 * @param array $properties
	 *
	 * @return array
	 */

	protected function tab( $file_or_options = [], $properties = [] ) {
		if ( ! is_string( $file_or_options ) && ! is_array( $file_or_options ) ) {
			return;
		}

		$tab = papi_tab( $file_or_options, $properties );

		// Tabs sometimes will be in $tab->options['options'] when you use a tab template in this method
		// and using the return value of papi_tab function is used.
		//
		// This should be fixed later, not a priority for now since this works.
		if ( is_object( $tab ) && isset( $tab->options ) && isset( $tab->options['options'] ) ) {
			$tab = (object) $tab->options;
		}

		if ( isset( $tab->options ) ) {
			$tab->options = papi_esc_html( $tab->options );
		}

		return $tab;
	}

	/**
	 * Load template file.
	 *
	 * @param string $file
	 * @param array $values
	 *
	 * @return array
	 */

	protected function template( $file, $values = [] ) {
		return papi_template( $file, $values );
	}
}
