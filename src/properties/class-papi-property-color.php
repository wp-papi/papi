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
			'change'        => false,
			'clear'         => false,
			'default_color' => false,
			'hide'          => false,
			'mode'          => 'hsv',
			'palettes'      => true,
			'slider'        => 'horizontal',
			'show_input'    => false,
			'type'          => 'full',
			'width'         => 255
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
				'data-settings' => $settings,
				'id'            => $this->html_id(),
				'name'          => $this->html_name(),
				'type'          => $settings->show_input === true ? 'text' : 'hidden',
				'value'         => $value,
			] )

		] );
	}
}
