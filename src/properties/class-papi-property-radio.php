<?php

/**
 * HTML radio buttons property that can handle one or
 * more radio buttons.
 */
class Papi_Property_Radio extends Papi_Property {

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function format_value( $value, $slug, $post_id ) {
		return papi_cast_string_value( $value );
	}

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
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();
		$value    = papi_cast_string_value( $this->get_value() );

		// Override selected setting with
		// database value if not null.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		echo '<div class="papi-property-radio">';

		foreach ( $settings->items as $key => $value ) {
			$key = is_numeric( $key ) ? $value : $key;

			papi_render_html_tag( 'p', [
				papi_html_tag( 'label', [
					'class' => 'light',
					'for'   => esc_attr( $this->html_id( $key ) ),

					papi_html_tag( 'input', [
						'id'      => esc_attr( $this->html_id( $key ) ),
						'name'    => esc_attr( $this->html_name() ),
						'type'    => 'radio',
						'checked' => $value === $settings->selected,
						'value'   => $value
					] ),

					esc_html( papi_convert_to_string( $key ) )
				] )
			] );
		}

		echo '</div>';
	}
}
