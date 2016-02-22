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
			'html' => '',
			'save' => false
		];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();
		$html     = papi_maybe_get_callable_value( $settings->html );

		if ( $settings->save ) {
			$value = $this->get_value();

			if ( ! empty( $value ) && is_string( $value ) ) {
				$html = $value;
			}

			papi_render_html_tag( 'input', [
				'name'  => $this->html_name(),
				'type'  => 'hidden',
				'value' => $html
			] );
		}

		papi_render_html_tag( 'div', [
			'data-papi-rule' => $this->html_name(),
			'class'          => 'property-html',
			$html
		] );
	}
}
