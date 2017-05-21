<?php

/**
 * HTML text input property.
 */
class Papi_Property_String extends Papi_Property {

	/**
	 * The input type to use.
	 *
	 * @var string
	 */
	public $input_type = 'text';

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
		if ( ! $this->get_setting( 'allow_html' ) && $this->input_type === 'text' ) {
			$value = papi_maybe_json_decode( maybe_unserialize( $value ) );

			if ( ! is_string( $value ) ) {
				$value = '';
			}

			$value = wp_strip_all_tags( $value );
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
			'allow_html'  => false,
			'placeholder' => ''
		];
	}

	/**
	 * Get value from the database.
	 *
	 * @return string
	 */
	public function get_value() {
		$value = $this->format_value(
			parent::get_value(),
			$this->get_slug(),
			papi_get_post_id()
		);

		return $this->get_setting( 'allow_html' ) ? $value : esc_html( $value );
	}

	/**
	 * Render property html.
	 */
	public function html() {
		papi_render_html_tag( 'input', [
			'id'          => esc_attr( $this->html_id() ),
			'name'        => esc_attr( $this->html_name() ),
			'type'        => esc_attr( $this->input_type ),
			'value'       => $this->get_value(),
			'placeholder' => esc_attr( $this->get_setting( 'placeholder' ) )
		] );
	}
}
