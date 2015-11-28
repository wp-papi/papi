<?php

/**
 * Papi Content Type is a base class that can
 * register boxes with properties.
 */
class Papi_Content_Type extends Papi_Core_Type {

	/**
	 * The array of meta boxes to register.
	 *
	 * @var array
	 */
	protected $boxes = [];

	/**
	 * Load all boxes.
	 *
	 * @var bool
	 */
	protected $load_boxes = false;

	/**
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'content';

	/**
	 * The constructor.
	 *
	 * Load a content type by the file.
	 *
	 * @param string $file_path
	 */
	public function __construct( $file_path = '' ) {
		// Try to load the file if the file path is empty.
		if ( empty( $file_path ) ) {
			$page_type = papi_get_content_type_id();
			$file_path = papi_get_file_path( $page_type );
		}

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

		if ( is_callable( $properties ) ) {
			$properties = call_user_func( $properties );
		}

		// Check and convert all non properties objects to properties objects.
		$properties = $this->convert_properties( $properties );

		// Create a core box instance and add it to the boxes array.
		array_push( $this->boxes, new Papi_Core_Box( $options, $properties ) );
	}

	/**
	 * Call parent register if it exists
	 * to collect boxes on the parent content type.
	 */
	private function call_parent_register() {
		$parent_class = get_parent_class( $this );

		if ( ! method_exists( $parent_class, 'register' ) ) {
			return;
		}

		$parent = new $parent_class();
		$parent->register();
		$this->boxes = $parent->get_boxes();
	}

	/**
	 * Convert properties to properties objects.
	 *
	 * @todo Refactor this method.
	 *
	 * @param  array|object $properties
	 *
	 * @return array
	 */
	private function convert_properties( $properties ) {
		if ( is_array( $properties ) ) {
			if ( isset( $properties['type'] ) ) {
				$properties = [$properties];
			} else if ( isset( $properties[0] ) && $properties[0] instanceof Papi_Core_Tab ) {
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
	 * Merge boxes with same title.
	 *
	 * @param  array $boxes
	 *
	 * @return array
	 */
	protected function merge_boxes( array $boxes ) {
		$result = [];

		foreach ( $boxes as $box ) {

			if ( ! isset( $result[$box->id] ) ) {
				$result[$box->id] = $box;
				continue;
			}

			foreach ( $box->properties as $property ) {
				$result[$box->id]->properties[] = $property;
			}
		}

		return array_values( $result );
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

			$this->call_parent_register();
			$this->register();
		}

		$this->boxes = $this->merge_boxes( $this->boxes );
		$this->boxes = papi_sort_order( array_reverse( $this->boxes ) );

		return $this->boxes;
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
		$boxes = $this->get_boxes( true );
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
			foreach ( $box->properties as $property ) {
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
	 * Check if the content type is a singleton.
	 *
	 * @return bool
	 */
	public function singleton() {
		return false;
	}

	/**
	 * Add a new tab.
	 *
	 * @param  mixed $file_or_options
	 * @param  array $properties
	 *
	 * @return null|Papi_Core_Tab
	 */
	protected function tab( $file_or_options = [], $properties = [] ) {
		if ( ! is_string( $file_or_options ) && ! is_array( $file_or_options ) ) {
			return;
		}

		$tab = papi_tab( $file_or_options, $properties );

		// Since `papi_tab` can be used in a boxes file we need to this check
		// so we can attach properties array if it's missing.
		if ( empty( $tab->properties ) && isset( $file_or_options['properties'] ) ) {
			$tab->properties = $file_or_options['properties'];
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
