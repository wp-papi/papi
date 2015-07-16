<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Dropdown.
 *
 * @package Papi
 */

class Papi_Property_Dropdown extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'placeholder' => '',
			'items'       => [],
			'selected'    => [],
			'select2'     => true
		];
	}

	/**
	 * Get dropdown items.
	 *
	 * @return array
	 */

	protected function get_items() {
		return papi_to_array( $this->get_setting( 'items', [] ) );
	}

	/**
	 * Display property html.
	 */

	public function html() {
		$settings     = $this->get_settings();
		$value        = $this->get_value();
		$options_html = [];

		// Override selected setting with
		// database value if not empty.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		$classes = 'papi-fullwidth';

		if ( $settings->select2 ) {
			$classes = ' papi-component-select2';
		}


		if ( ! empty( $settings->placeholder ) ) {
			$options_html[] = papi_html_tag( 'option', [
				'value' => ''
			] );
		}

		// Create option html tags for all items.
		foreach ( $this->get_items() as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			if ( papi_is_empty( $key ) ) {
				continue;
			}

			$options_html[] = papi_html_tag( 'option', [
				'selected' => $value === $settings->selected ? 'selected' : null,
				'value'    => $value,
				$key
			] );
		}

		papi_render_html_tag( 'select', [
			'class'            => $classes,
			'data-allow-clear' => true,
			'data-placeholder' => $settings->placeholder,
			'data-width'       => '100%',
			'id'               => $this->html_id(),
			'name'             => $this->html_name(),
			$options_html
		] );
	}

}
