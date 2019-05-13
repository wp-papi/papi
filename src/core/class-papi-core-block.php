<?php

class Papi_Core_Block extends Papi_Core_Box {

	/**
	 * Block attributes.
	 *
	 * @var string
	 */
	public $attributes = [];

	/**
	 * Block category.
	 *
	 * @var string
	 */
	public $category = 'common';

	/**
	 * Block description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Block icon.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Block keywords.
	 *
	 * @var array
	 */
	public $keywords = [];

	/**
	 * Block name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Block render callback.
	 *
	 * @var mixed
	 */
	public $render_callback = null;

	/**
	 * Block styles.
	 *
	 * @var string
	 */
	public $styles = [];

	/**
	 * Block supports.
	 *
	 * @var string
	 */
	public $supports = [];

	/**
	 * The constructor.
	 *
	 * @param array $args
	 * @param array $properties
	 */
	public function __construct( array $args = [], array $properties = [] ) {
		parent::__construct( $args, $properties );
		$this->setup_block( $args );
	}

	/**
	 * Setup block.
	 *
	 * @param array $args
	 */
	protected function setup_block( array $args = [] ) {
		// Prevent js errors with to many keywords.
		$this->keywords = array_slice( $this->keywords, 0, 3 );

		// Set block name.
		$this->name = sprintf( 'papi/%s', str_replace( '_', '-', substr( $this->id, 1 ) ) );

		// Add default block support.
		$this->supports = wp_parse_args( $args['support'], [
			'align' => true,
			'html'  => false,
			'mode'  => true,
		] );

		// Add attributes.
		$this->attributes = $this->get_attributes();

		// Add title.
		$this->title = $this->get_title();
	}

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_attributes() {
		$value = [];

		foreach ( $this->properties as $property ) {
			$slug = $property->get_slug( true );

			$values[$slug] = [
				'type' => $property->convert_type,
			];
		}

		return $values;
	}

	/**
	 * Get block title.
	 *
	 * @return string
	 */
	protected function get_title() {
		$title = $this->title;

		if ( $this->get_option( 'required' ) ) {
			$title .= papi_property_required_html(
				$this->properties[0],
				true
			);
		}

		return $title;
	}
}
