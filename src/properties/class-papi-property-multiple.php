<?php

/**
 * Multiple property that render child properties
 * in a table.
 */
class Papi_Property_Multiple extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'items' => []
		];
	}

	/**
	 * Get settings properties.
	 *
	 * @return array
	 */
	protected function get_settings_properties() {
		$settings = $this->get_settings();

		if ( is_null( $settings ) ) {
			return [];
		}

		return papi_to_array( $settings->items );
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$properties = $this->get_settings_properties();
		echo '<div class="papi-property-multiple">';
			papi_render_properties( $properties );
		echo '</div>';
	}
}
