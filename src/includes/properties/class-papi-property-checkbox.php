<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Checkbox class.
 *
 * @package Papi
 */
class Papi_Property_Checkbox extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'array';

	/**
	 * The default value.
	 *
	 * @var array
	 */
	public $default_value = [];

	/**
	 * Format the value of the property before it's returned to the application.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_string( $value ) && ! papi_is_empty( $value ) ) {
			return [$value];
		}

		if ( ! is_array( $value ) ) {
			return $this->default_value;
		}

		return $value;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'items'    => [],
			'selected' => []
		];
	}

	/**
	 * Display property html.
	 */
	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		// Override selected setting with
		// database value if not empty.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		$settings->selected = papi_to_array( $settings->selected );

		foreach ( $settings->items as $key => $value ) {

			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			papi_render_html_tag( 'label', [
				'class' => 'light',
				'for'   => $this->html_id( $key ),

				papi_html_tag( 'input', [
					'checked' => in_array( $value, $settings->selected ) ? 'checked' : null,
					'id'      => $this->html_id( $key ),
					'name'    => $this->html_name() . '[]',
					'type'    => 'checkbox',
					'value'   => $value
				] ),

				$key
			] );

			papi_render_html_tag( 'br' );
		}
	}

	/**
	 * Import value to the property.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( is_string( $value ) && ! papi_is_empty( $value ) ) {
			return [$value];
		}

		if ( ! is_array( $value ) ) {
			return;
		}

		return $value;
	}
}
