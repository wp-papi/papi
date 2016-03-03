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
			$num = $i === 0 ? '' : '_' . $i + 1;
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
		return is_admin() ? $value : apply_filters( 'the_content', $value );
	}

	/**
	 * Filter TinyMCE buttons (Visual tab).
	 *
	 * @return array
	 */
	public function mce_buttons() {
		return papi_to_array( $this->get_setting( current_filter(), [] ) );
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

		// Add mce buttons filters.
		$this->add_mce_buttons();

		// Filter the second-row list of TinyMCE buttons (Visual tab).
		add_filter( 'mce_buttons_2', function () {
			return papi_to_array( $this->get_setting( 'mce_buttons_2', [] ) );
		} );

		wp_editor( $value, $id, [
			'textarea_name' => $this->html_name(),
			'media_buttons' => true
		] );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_filter( 'mce_external_plugins', '__return_empty_array' );
		}
	}
}
