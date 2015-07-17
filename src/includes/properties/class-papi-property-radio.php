<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Radio.
 *
 * @package Papi
 */

class Papi_Property_Radio extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'items'    => [],
			'selected' => ''
		];
	}

	/**
	 * Display property html.
	 */

	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		// Override selected setting with
		// database value if not null.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		foreach ( $settings->items as $key => $value ) {

			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			papi_render_html_tag( 'label', [
				'class' => 'light',
				'for'   => $this->html_id( $key ),

				papi_render_html_tag( 'input', [
					'id'      => $this->html_id( $key ),
					'name'    => $this->html_name(),
					'type'    => 'radio',
					'checked' => $value === $settings->selected ? 'checked' : null,
					'value'   => $value
				] ),

				$key
			] );

			papi_render_html_tag( 'br' );
		}
	}
}
