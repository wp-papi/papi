<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property User class.
 *
 * @package Papi
 */

class Papi_Property_User extends Papi_Property_Dropdown {

	/**
	 * The convert type.
	 *
	 * @var string
	 */

	public $convert_type = 'int';

	/**
	 * Format the value of the property before it's returned to the theme.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) ) {
			return new WP_User( (int) $value );
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

		if ( $user instanceof WP_User === false ) {
			return 0;
		}

		return $user->ID;
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

		return $items;
	}

}
