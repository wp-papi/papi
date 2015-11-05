<?php

/**
 * Papi data type is a base class that can
 * register boxes with properties.
 */
class Papi_Data_Type extends Papi_Core_Type {

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
	protected $load_boxes = false;

	/**
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'data';

	/**
	 * The constructor.
	 *
	 * Load a page type by the file.
	 *
	 * @param string $file_path
	 */
	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
	}

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

		list( $options, $properties ) = papi_get_options_and_properties(
			$file_or_options,
			$properties,
			true
		);

		// Check so we have a post the to add the box to.
		// @codeCoverageIgnoreStart
		if ( ! $this->load_boxes ) {
			return;
		}
		// @codeCoverageIgnoreEnd

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

		$options['title'] = papi_esc_html(
			isset( $options['title'] ) ? $options['title'] : ''
		);

		array_push( $this->boxes, [
			$options,
			$properties,
			'sort_order' => $sort_order,
			'title'      => $options['title']
		] );
	}

	/**
	 * Convert properties to properties objects.
	 *
	 * @param  array|object $properties
	 *
	 * @return array
	 */
	private function convert_properties( $properties ) {
		if ( is_array( $properties ) ) {
			if ( isset( $properties['type'] ) ) {
				$properties = [$properties];
			} else if ( isset( $properties[0]->tab ) && $properties[0]->tab ) {
				foreach ( $properties as $index => $items ) {
					$items->properties = array_map(
						'papi_get_property_type',
						$items->properties
					);
				}

				return $properties;
			}
		}

		if ( is_object( $properties ) ) {
			$properties = papi_get_property_type( $properties );
		}

		if ( papi_is_property( $properties ) ) {
			$properties = [$properties];
		}

		$properties = is_array( $properties ) ? $properties : [];
		$properties = array_map( 'papi_get_property_type', $properties );

		return array_filter( $properties, 'papi_is_property' );
	}

	/**
	 * Get boxes from the page type.
	 *
	 * @return array
	 */
	public function get_boxes() {
		if ( empty( $this->boxes ) && $this->load_boxes === false ) {
			if ( ! method_exists( $this, 'register' ) ) {
				return [];
			}

			$this->load_boxes = true;

			$this->register();
		}

		return papi_sort_order( array_reverse( $this->boxes ) );
	}

	/**
	 * Get child property.
	 *
	 * @param  array  $items
	 * @param  string $slug
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

			$property = papi_get_property_type( $property );

			if ( papi_is_property( $property ) && $property->match_slug( $slug ) ) {
				return papi_get_property_type( $property );
			}
		}

		return $result;
	}

	/**
	 * Get property from page type.
	 *
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return null|Papi_Property
	 */
	public function get_property( $slug, $child_slug = '' ) {
		$boxes = $this->get_boxes();
		$parts = preg_split( '/\[\d+\]/', $slug );
		$parts = array_map( function ( $part ) {
			return preg_replace( '/(\[|\])/', '', $part );
		}, $parts );

		if ( count( $parts ) > 1 ) {
			$property = null;

			for ( $i = 0, $l = count( $parts ); $i < $l; $i++ ) {
				$child    = isset( $parts[$i + 1] ) ? $parts[$i + 1] : '';
				$property = $this->get_property( $parts[$i], $child );

				if ( isset( $parts[$i + 1] ) ) {
					$i++;
				}
			}

			return $property;
		}

		if ( empty( $boxes ) ) {
			return;
		}

		foreach ( $boxes as $box ) {
			$properties = isset( $box[1][0]->properties ) ?
				$box[1][0]->properties : $box[1];

			foreach ( $properties as $property ) {
				$property = papi_get_property_type( $property );

				if ( papi_is_property( $property ) && $property->match_slug( $slug ) ) {
					if ( empty( $child_slug ) ) {
						return $property;
					}

					$result = $this->get_child_property(
						$property->get_child_properties(),
						$child_slug
					);

					if ( is_object( $result ) ) {
						return papi_get_property_type( $result );
					}
				}
			}
		}
	}

	/**
	 * Add new property to the page using array or rendering property template file.
	 *
	 * @param  array|string $file_or_options
	 * @param  array $values
	 *
	 * @return null|Papi_Property
	 */
	protected function property( $file_or_options = [], $values = [] ) {
		return papi_property( $file_or_options, $values );
	}

	/**
	 * Add a new tab.
	 *
	 * @param  mixed $file_or_options
	 * @param  array $properties
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
	 * @param  string $file
	 * @param  array  $values
	 *
	 * @return array
	 */
	protected function template( $file, $values = [] ) {
		return papi_template( $file, $values );
	}
}
