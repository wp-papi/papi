<?php

/**
 * HTML5 number input property.
 */
class Papi_Property_Number extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'int';

	/**
	 * Get convert type.
	 *
	 * @return string
	 */
	public function get_convert_type() {
		$value = $this->get_value();

		return is_float( $value ) ? 'float' : $this->convert_type;
	}

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return float|int
	 */
	public function format_value( $value, $slug, $post_id ) {
		$value = is_string( $value ) && is_numeric( $value ) ? $value + 0 : $value;

		if ( is_float( $value ) ) {
			return floatval( $value );
		} else {
			return intval( $value );
		}
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'max'  => '',
			'min'  => '',
			'step' => 'any',
			'type' => 'number'
		];
	}

	/**
	 * Get value from the database.
	 *
	 * @return float|int
	 */
	public function get_value() {
		return $this->format_value(
			parent::get_value(),
			$this->get_slug(),
			papi_get_post_id()
		);
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		// If range type is used change the default values if empty.
		if ( $settings->type === 'range' ) {
			$settings->max  = papi_is_empty( $settings->max )
				? 100 : $settings->max;
			$settings->min  = papi_is_empty( $settings->min )
				? 0 : $settings->min;
			$settings->step = papi_is_empty( $settings->step )
				? 1 : $settings->step;
		}

		if ( $settings->min !== 0 && $value < $settings->min ) {
			$value = $settings->min;
		}

		papi_render_html_tag( 'input', [
			'id'    => $this->html_id(),
			'max'   => $settings->max,
			'min'   => $settings->min,
			'name'  => esc_attr( $this->html_name() ),
			'step'  => $settings->step,
			'type'  => esc_attr( $settings->type ),
			'value' => $value
		] );
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
		return $this->format_value( $value, $slug, $post_id );
	}
}
