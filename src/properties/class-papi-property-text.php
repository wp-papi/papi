<?php

/**
 * HTML textarea property.
 */
class Papi_Property_Text extends Papi_Property {

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
		if ( ! $this->get_setting( 'allow_html' ) ) {
			$value = maybe_unserialize( $value );

			if ( ! is_string( $value ) ) {
				$value = '';
			}

			$value = wp_strip_all_tags( $value );

			if ( ! papi_is_admin() ) {
				$value = $this->get_setting( 'nl2br' ) ? nl2br( $value ) : $value;
			}
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
			'allow_html' => false,
			'nl2br'      => true
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
		papi_render_html_tag( 'textarea', [
			'class' => 'papi-property-text',
			'id'    => esc_attr( $this->html_id() ),
			'name'  => esc_attr( $this->html_name() ),
			$this->get_value()
		] );
	}
}
