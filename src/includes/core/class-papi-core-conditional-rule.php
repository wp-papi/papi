<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Core Conditional Rule.
 *
 * @package Papi
 */

class Papi_Core_Conditional_Rule {

	/**
	 * The operator.
	 *
	 * @var string
	 */

	public $operator;

	/**
	 * The slug.
	 *
	 * @var string
	 */

	public $slug;

	/**
	 * The value.
	 *
	 * @var mixed
	 */

	public $value;

	/**
	 * The constructor.
	 *
	 * @param array $rule
	 */

	public function __construct( array $rule ) {
		$this->setup( $rule );
	}

	/**
	 * Setup the rule and assign properties with values.
	 *
	 * @param  array  $rule
	 */

	private function setup( array $rule ) {
		foreach ( $rule as $key => $value ) {
			$this->$key = $value;
		}
	}
}
