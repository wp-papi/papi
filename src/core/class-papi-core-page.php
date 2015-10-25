<?php

/**
 * Core class that implements a Papi page.
 */
abstract class Papi_Core_Page extends Papi_Container {

	/**
	 * Type post.
	 *
	 * @var string
	 */
	const TYPE_POST = 'post';

	/**
	 * Type option.
	 *
	 * @var string
	 */
	const TYPE_OPTION = 'option';

	/**
	 * The WordPress post id if it exists.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * The type of page.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Get Papi property value.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function __get( $slug ) {
		return $this->get_value( $slug );
	}

	/**
	 * Get value from property.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function get_value( $slug ) {
		$slug  = papi_remove_papi( $slug );
		$value = papi_get_property_meta_value( $this->id, $slug, $this->type );
		return $this->convert( $slug, $value );
	}

	/**
	 * Convert property value with the property type converter.
	 *
	 * @param  string $slug
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	protected function convert( $slug, $value ) {
		$property = $this->get_property( $slug );

		// If no property type is found, just return null.
		if ( ! papi_is_property( $property ) ) {
			return;
		}

		if ( papi_is_empty( $value ) ) {
			if ( ! papi_is_empty( $property->get_option( 'value' ) ) ) {
				return $property->get_option( 'value' );
			}
			return;
		}

		// A property need to know about the page.
		$property->set_page( $this );

		// Run load value method right after the value has been loaded from the database.
		$value = $property->load_value( $value, $slug, $this->id );

		if ( $this->type !== self::TYPE_OPTION ) {
			$value = papi_filter_load_value(
				$property->type,
				$value,
				$slug,
				$this->id
			);
		}

		// Format the value from the property class.
		$value = $property->format_value( $value, $slug, $this->id );

		if ( ! is_admin() || $this->type !== self::TYPE_OPTION ) {
			$value = papi_filter_format_value(
				$property->type,
				$value,
				$slug,
				$this->id
			);
		}

		if ( is_array( $value ) ) {
			$value = array_filter( $value );
		}

		return $value;
	}

	/**
	 * Check if the `$type` match the page type.
	 *
	 * @param  string $type
	 *
	 * @return bool
	 */
	public function is( $type ) {
		return $this->type === $type;
	}

	/**
	 * Get page from factory.
	 *
	 * @param  int    $post_id
	 * @param  string $type
	 *
	 * @return mixed
	 */
	public static function factory( $post_id, $type = self::TYPE_POST ) {
		if ( papi_is_option_page() ) {
			$type = self::TYPE_OPTION;
		}

		$class_suffix = '_' . ucfirst( $type ) . '_Page';
		$class_name   = 'Papi' . $class_suffix;

		if ( ! class_exists( $class_name ) ) {
			return;
		}

		$post_id = papi_get_post_id( $post_id );
		$page = new $class_name( $post_id );
		$page->set_type( $type );

		if ( ! $page->valid() ) {
			return;
		}

		return $page;
	}

	/**
	 * Get property from page type.
	 *
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return Papi_Property
	 */
	abstract public function get_property( $slug, $child_slug = '' );

	/**
	 * Set type.
	 *
	 * @param string $type
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * Check if it's a valid page.
	 *
	 * @return bool
	 */
	abstract public function valid();

	/**
	 * Check if the page has a valid type.
	 *
	 * @return bool
	 */
	protected function valid_type() {
		$type = strtoupper( $this->type );
		return defined( "self::TYPE_$type" );
	}
}
