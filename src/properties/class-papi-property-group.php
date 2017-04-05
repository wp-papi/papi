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

		return array_filter( papi_to_array( $settings->items ), 'papi_is_property' );
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

		echo '<div class="papi-property-group" data-papi-rule="' . esc_html( $this->html_name() ) . '">';
			papi_render_properties( $properties );
		echo '</div>';
	}

	/**
	 * Prepare properties and set right slug and value.
	 *
	 * @param  array $properties
	 *
	 * @return array
	 */
	protected function prepare_properties( $properties ) {
		$result = [];
		$value  = $this->get_value();
		$value  = is_array( $value ) ? $value : [];

		foreach ( $properties as $property ) {
			$render_property = clone $property->get_options();
			$value_slug      = $property->get_slug( true );

			if ( array_key_exists( $value_slug, $value ) ) {
				$render_property->value = $value[$value_slug];
			} else {
				$render_property->value = null;
			}

			$render_property->slug = $this->html_name( $property, $this->counter );

			$result[] = $render_property;
		}

		return $result;
	}
}
