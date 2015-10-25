<?php

/**
 * HTML property that can display html in the admin.
 */
class Papi_Property_Html extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'html' => ''
		];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();

		papi_render_html_tag( 'div', [
			'data-papi-rule' => $this->html_name(),
			'class'          => 'property-html',
			papi_maybe_get_callable_value( $settings->html )
		] );
	}
}
