<?php

/**
 * Color property that implements WordPress color
 * picker as a property.
 */
class Papi_Property_Color extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'palettes'   => [],
			'show_input' => true
		];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		papi_render_html_tag( 'div', [
			'class' => 'papi-property-color-picker',

			papi_html_tag( 'input', [
				'data-palettes' => $settings->palettes,
				'id'            => $this->html_id(),
				'name'          => $this->html_name(),
				'type'          => $settings->show_input === true ? 'text' : 'hidden',
				'value'         => $value,
			] )

		] );
	}
}
