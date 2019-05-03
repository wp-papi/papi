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
	 * Setup arguments.
	 *
	 * @param  array $args
	 */
	protected function setup_args( array $args ) {
		parent::setup_args( $args );

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
	}
}
