<?php

/**
 * Dropdown property.
 */
class Papi_Property_Dropdown extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'mixed';

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( ! $this->get_setting( 'multiple' ) ) {
			return $this->is_string_items() ? $value : papi_cast_string_value( $value );
		}

		$value = is_array( $value ) ? $value : [];

		if ( ! $this->is_string_items() ) {
			$value = array_map( 'papi_cast_string_value', $value );
		}

		return $value;
	}

	/**
	 * Determine if items are strings or not.
	 *
	 * @return bool
	 */
	public function is_string_items() {
		$items = $this->get_items();

		if ( empty( $items ) ) {
			return false;
		}

		$items = array_values( $items );

		return is_string( $items[0] );
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'placeholder' => '',
			'items'       => [],
			'multiple'    => false,
			'selected'    => [],
			'select2'     => true
		];
	}

	/**
	 * Get dropdown items.
	 *
	 * @return array
	 */
	protected function get_items() {
		return papi_to_array( $this->get_setting( 'items', [] ) );
	}

	/**
	 * Render property html.
	 */
	public function html() {
		// Setup variables needed.
		$settings     = $this->get_settings();
		$value        = $this->get_value();
		$options_html = [];

		// Properties that extends dropdown property
		// maybe don't have this setting.
		if ( ! isset( $settings->multiple ) ) {
			$settings->multiple = false;
		}

		// Override selected setting with
		// database value if not empty.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		$classes = 'papi-fullwidth';

		if ( $settings->select2 ) {
			$classes .= ' papi-component-select2';
		}

		// Add placeholder if any.
		if ( ! empty( $settings->placeholder ) ) {
			$options_html[] = papi_html_tag( 'option', [
				'value' => '',
				$settings->placeholder
			] );
		}

		// Create option html tags for all items.
		foreach ( $this->get_items() as $key => $value ) {
			$key = is_numeric( $key ) ? $value : $key;

			if ( papi_is_empty( $key ) ) {
				continue;
			}

			// When a multiple we need to check with
			// `in_array` since the value is a array.
			if ( $settings->multiple ) {
				$selected = in_array( $value, $settings->selected, true );
			} else {
				$selected = papi_convert_to_string( $value ) === papi_convert_to_string( $settings->selected );
			}

			$options_html[] = papi_html_tag( 'option', [
				'data-edit-url' => get_edit_post_link( $value ),
				'selected'      => $selected ? 'selected' : null,
				'value'         => $value,
				esc_html( papi_convert_to_string( $key ) )
			] );
		}

		// When selectize is empty this will be used.
		papi_render_html_tag( 'input', [
			'name' => $this->html_name() . ( $settings->multiple ? '[]' : '' ),
			'type' => 'hidden'
		] );

		papi_render_html_tag( 'select', [
			'class'            => $classes,
			'data-allow-clear' => ! empty( $settings->placeholder ),
			'data-placeholder' => $settings->placeholder,
			'data-width'       => '100%',
			'id'               => $this->html_id() . ( $settings->multiple ? '[]' : '' ),
			'multiple'         => $settings->multiple ? 'multiple' : null,
			'name'             => $this->html_name() . ( $settings->multiple ? '[]' : '' ),
			$options_html
		] );
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
		if ( ! ( $value = $this->prepare_value( $value ) ) ) {
			return;
		}

		$value = maybe_unserialize( $value );

		return papi_maybe_json_decode( $value, $this->get_setting( 'multiple' ) );
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
		$value = maybe_unserialize( $value );

		return papi_maybe_json_decode( $value, $this->get_setting( 'multiple' ) );
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
		if ( ! ( $value = $this->prepare_value( $value ) ) ) {
			return;
		}

		return papi_maybe_json_encode( $value );
	}
}
