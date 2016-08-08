<?php

/**
 * HTML5 url input property.
 */
class Papi_Property_Url extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'mediauploader' => false
		];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();

		papi_render_html_tag( 'input', [
			'class'   => $settings->mediauploader ? 'papi-url-media-input' : null,
			'id'      => esc_attr( $this->html_id() ),
			'name'    => esc_attr( $this->html_name() ),
			'type'    => 'url',
			'value'   => $this->get_value()
		] );

		if ( $settings->mediauploader ) {
			echo '&nbsp;';

			papi_render_html_tag( 'input', [
				'class'            => 'button papi-url-media-button',
				'data-papi-action' => 'mediauploader',
				'id'               => esc_attr( $this->html_id() ),
				'name'             => esc_attr( $this->html_name() . '_button' ),
				'type'             => 'button',
				'value'            => esc_attr__( 'Select file', 'papi' )
			] );
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
		return $this->load_value( $value, $slug, $post_id );
	}

	/**
	 * Change value after it's loaded from the database.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function load_value( $value, $slug, $post_id ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}
	}

	/**
	 * Update value before it's saved to the database.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function update_value( $value, $slug, $post_id ) {
		return $this->load_value( $value, $slug, $post_id );
	}
}
