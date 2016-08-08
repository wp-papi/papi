<?php

/**
 * HTML checkbox property that can handle one or
 * more checkboxes.
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
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_string( $value ) && ! papi_is_empty( $value ) ) {
			return [papi_cast_string_value( $value )];
		}

		if ( ! is_array( $value ) ) {
			return $this->default_value;
		}

		return array_map( 'papi_cast_string_value', $value );
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
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();
		$value    = papi_cast_string_value( $this->get_value() );

		// Override selected setting with
		// database value if not empty.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		$settings->selected = papi_to_array( $settings->selected );

		echo '<div class="papi-property-checkbox">';

		foreach ( $settings->items as $key => $value ) {
			$key = is_numeric( $key ) ? $value : $key;

			papi_render_html_tag( 'p', [
				papi_html_tag( 'label', [
					'class' => 'light',
					'for'   => esc_attr( $this->html_id( $key ) ),

					papi_html_tag( 'input', [
						'id'      => esc_attr( $this->html_id( $key ) ),
						'name'    => esc_attr( $this->html_name() ) . '[]',
						'type'    => 'hidden'
					] ),

					papi_html_tag( 'input', [
						'checked' => in_array( $value, $settings->selected, true ),
						'id'      => esc_attr( $this->html_id( $key ) ),
						'name'    => esc_attr( $this->html_name() ) . '[]',
						'type'    => 'checkbox',
						'value'   => esc_attr( $value )
					] ),

					esc_html( papi_convert_to_string( $key ) )
				] )
			] );
		}

		echo '</div>';
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
