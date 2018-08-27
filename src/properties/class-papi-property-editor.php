<?php

/**
 * WordPress editor property.
 */
class Papi_Property_Editor extends Papi_Property {

	/**
	 * Add filters to filter TinyMCE buttons.
	 */
	protected function add_mce_buttons() {
		for ( $i = 0; $i < 4; $i++ ) {
			$num = $i === 0 ? '' : '_' . ( $i + 1 );
			add_filter( 'mce_buttons' . $num, [$this, 'mce_buttons'] );
		}
	}

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
		return papi_is_admin() ? $value : apply_filters( 'the_content', $value );
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'mce_buttons'      => [],
			'mce_buttons_2'    => [],
			'mce_buttons_3'    => [],
			'mce_buttons_4'    => [],
			'media_buttons'    => true,
			'teeny'            => false,
			'drag_drop_upload' => true
		];
	}

	/**
	 * Filter TinyMCE buttons (Visual tab).
	 *
	 * @param  array $buttons
	 *
	 * @return array
	 */
	public function mce_buttons( array $buttons = [] ) {
		if ( $new = papi_to_array( $this->get_setting( current_filter(), [] ) ) ) {
			return $new;
		}

		return $buttons;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$value = $this->get_value();
		$value = html_entity_decode( $value );
		$id    = str_replace(
			'[',
			'',
			str_replace( ']', '', $this->html_name() )
		) . '-' . uniqid();

		// Add `mce_buttons` filters.
		$this->add_mce_buttons();

		wp_editor( $value, $id, [
			'textarea_name'    => esc_attr( $this->html_name() ),
			'media_buttons'    => $this->get_setting( 'media_buttons', true ),
			'teeny'            => $this->get_setting( 'teeny', false ),
			'drag_drop_upload' => $this->get_setting( 'drag_drop_upload', true ),
		] );

		// Remove `mce_buttons` filters.
		$this->reove_mce_buttons();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_filter( 'mce_external_plugins', '__return_empty_array' );
		}
	}

	/**
	 * Remove filters that filter TinyMCE buttons.
	 */
	protected function reove_mce_buttons() {
		for ( $i = 0; $i < 4; $i++ ) {
			$num = $i === 0 ? '' : '_' . ( $i + 1 );
			remove_filter( 'mce_buttons' . $num, [$this, 'mce_buttons'] );
		}
	}
}
