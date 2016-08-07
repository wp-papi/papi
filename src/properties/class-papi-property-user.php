<?php

/**
 * User property that populates a dropdown.
 */
class Papi_Property_User extends Papi_Property_Dropdown {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'int';

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return null|WP_User
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_object( $value ) && isset( $value->ID ) ) {
			$value = $value->ID;
		}

		if ( is_numeric( $value ) ) {
			return $value === 0 ? null : new WP_User( $value );
		}

		return $value;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'capabilities' => [],
			'placeholder'  => '',
			'select2'      => true
		];
	}

	/**
	 * Get value from database.
	 *
	 * @return int
	 */
	public function get_value() {
		$user = parent::get_value();

		if ( is_object( $user ) && isset( $user->ID ) ) {
			return $user->ID;
		}

		return 0;
	}

	/**
	 * Get users as dropdown items.
	 *
	 * @return array
	 */
	public function get_items() {
		$capabilities = papi_to_array( $this->get_setting( 'capabilities' ) );
		$users        = get_users();
		$items        = [];

		foreach ( $users as $user ) {
			$allcaps = $user->allcaps;

			if ( count( array_diff( $capabilities, array_keys( $allcaps ) ) ) === 0 ) {
				$items[$user->display_name] = $user->ID;
			}
		}

		ksort( $items );

		return $items;
	}

	/**
	 * Import value to the property.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return int|null
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( $value instanceof WP_User ) {
			$value = $value->ID;
		}

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}
	}

	/**
	 * Change value after it's loaded from the database.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return int
	 */
	public function load_value( $value, $slug, $post_id ) {
		return (int) $value;
	}

	/**
	 * Update value before it's saved to the database.
	 *
	 * @param mixed  $value
	 * @param string $slug
	 * @param int    $post_id
	 *
	 * @return int
	 */
	public function update_value( $value, $slug, $post_id ) {
		if ( $value instanceof WP_User ) {
			$value = $value->ID;
		}

		return (int) $value;
	}
}
