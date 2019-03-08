<?php

/**
 * Sidebar property that populates a dropdown.
 */
class Papi_Property_Sidebar extends Papi_Property_Dropdown {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'string';

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return string
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_admin() || ! $this->settings->render ) {
			return $value;
		}

		ob_start();
		if ( is_active_sidebar( $value ) ) {
			dynamic_sidebar( $value );
		}

		return ob_get_clean();
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'allow_clear'  => true,
			'placeholder'  => '',
			'render'       => true,
			'select2'      => true
		];
	}

	/**
	 * Get registered sidebars as dropdown items.
	 *
	 * @return array
	 */
	public function get_items() {
		global $wp_registered_sidebars;
		$items = [];

		foreach ( $wp_registered_sidebars as $item ) {
			$items[$item['name']] = $item['id'];
		}

		ksort( $items );

		return $items;
	}
}
