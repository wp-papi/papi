<?php

abstract class Papi_Admin_Entry {

	/**
	 * The instance.
	 *
	 * @var Papi_Admin_Entry
	 */
	protected static $instance;

	/**
	 * Create or get the instance.
	 *
	 * @return Papi_Admin_Entry
	 */
	public static function instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Setup admin entry.
	 *
	 * @return bool
	 */
	public function setup() {
		return true;
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
	}

	/**
	 * Setup filters.
	 */
	protected function setup_filters() {
	}
}
