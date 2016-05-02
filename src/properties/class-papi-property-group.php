<?php

/**
 * Group property that render child properties in a table.
 */
class Papi_Property_Group extends Papi_Property_Repeater {

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
		if ( ! is_array( $value ) ) {
			return [];
		}

		$value = parent::format_value( $value, $slug, $post_id );

		return array_shift( $value );
	}

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

		// Fix so group is not render over the title and description.
		if ( $this->get_option( 'layout' ) === 'vertical' ) {
			echo '<br />';
		}

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
	protected function prepare_properties( $properties ) {
		$value = $this->get_value();

		foreach ( $properties as $index => $property ) {
			$property = papi_property( $property );

			if ( ! papi_is_property( $property ) ) {
				unset( $properties[$index] );
				continue;
			}

			$slug = $property->get_slug( true );

			// Set the value if it exists.
			if ( isset( $value[$slug] ) ) {
				$property->value = $value[$slug];
			} else {
				$property->value = null;
			}

			// Create a array slug.
			$property->slug = sprintf( '%s[0][%s]', $this->slug, $slug );

			$properties[$index] = $property;
		}

		return $properties;
	}
}
