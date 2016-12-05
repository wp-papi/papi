<?php

class Papi_Core_Tab {

	/**
	 * The background of the tab.
	 *
	 * Possible values are: `white` or `grey`.
	 *
	 * By default if empty the background will be automatic,
	 * if first property has no sidebar it'll be white and
	 * if it has a sidebar it'll be grey.
	 *
	 * @var string
	 */
	public $background = '';

	/**
	 * Capabilities list.
	 *
	 * @var array
	 */
	public $capabilities = [];

	/**
	 * Tab icon.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * The core tab identifier.
	 *
	 * @var string
	 */
	public $id = '';

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
	 * Because of old code for tabs this
	 * property is required to exists on
	 * core tab class.
	 *
	 * @var bool
	 */
	public $tab = true;

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
	 * Setup arguments.
	 *
	 * @param  array $args
	 */
	protected function setup_args( array $args ) {
		foreach ( $args as $key => $value ) {
			if ( isset( $this->$key ) ) {
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
	protected function setup_properties( array $properties ) {
		$this->properties = array_merge( $this->properties, papi_populate_properties( $properties ) );
	}
}
