<?php

/**
 * Group property that render child properties in a table.
 */
class Papi_Property_Group extends Papi_Property {

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
		$properties = $this->prepare_properties( $properties );

		echo '<div class="papi-property-group">';
			papi_render_properties( $properties );
		echo '</div>';
	}

	/**
	 * Prepare properties and set right slugs.
	 *
	 * @param  array $properties
	 *
	 * @return array
	 */
	protected function prepare_properties( array $properties ) {
		$render_property_slug = $this->slug;
		preg_match( '/\[(\d+)\]/', $render_property_slug, $matches );

		if ( ! isset( $matches[1] ) ) {
			return $properties;
		}

		foreach ( $properties as $index => $property ) {
			if ( ! isset( $property['slug'] ) ) {
				unset( $properties[$index] );
				continue;
			}

			$parts = explode($matches[0], $render_property_slug);
			array_pop($parts);
			$parts[] = sprintf('[%s]', unpapify($property['slug']));
			$slug = implode($matches[0], $parts);
			$properties[$index]['slug'] = $slug;
		}

		return $properties;
	}
}
