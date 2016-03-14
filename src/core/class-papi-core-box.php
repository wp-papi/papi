<?php

class Papi_Core_Box {

	/**
	 * Capabilities list.
	 *
	 * @var array
	 */
	public $capabilities = [];

	/**
	 * Context.
	 *
	 * @var string
	 */
	public $context = 'normal';

	/**
	 * The core type identifier.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Custom box options.
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Priority.
	 *
	 * @var string
	 */
	public $priority = 'default';

	/**
	 * Box properties.
	 *
	 * @var array
	 */
	public $properties = [];

	/**
	 * The sort order of the core box.
	 *
	 * @var int
	 */
	public $sort_order = 1000;

	/**
	 * The title of the box.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The constructor.
	 *
	 * @param array $args
	 * @param array $properties
	 */
	public function __construct( array $args = [], array $properties = [] ) {
		$this->setup_args( $args );
		$this->setup_properties( $properties );
	}

	/**
	 * Get box option.
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function get_option( $key ) {
		return isset( $this->options[$key] ) ? $this->options[$key] : null;
	}

	/**
	 * Set box option.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set_option( $key, $value ) {
		$this->options[$key] = $value;
	}

	/**
	 * Setup arguments.
	 *
	 * @param  array $args
	 */
	private function setup_args( array $args ) {
		$excluded_keys = ['options', 'properties'];

		foreach ( $args as $key => $value ) {
			if ( isset( $this->$key ) && ! in_array( $key, $excluded_keys ) ) {
				$this->$key = papi_esc_html( $value );
			}
		}

		if ( empty( $this->id ) ) {
			$this->id = strtolower( papi_f( papi_underscorify( papify( $this->title ) ) ) );
		}
	}

	/**
	 * Setup properties.
	 *
	 * @param  array $properties
	 */
	private function setup_properties( array $properties ) {
		$this->properties = papi_populate_properties( $properties );
	}
}
