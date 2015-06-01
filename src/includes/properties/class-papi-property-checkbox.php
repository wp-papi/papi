<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Checkbox.
 *
 * @package Papi
 */

class Papi_Property_Checkbox extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */

	public $convert_type = 'array';

	/**
	 * The default value.
	 *
	 * @var array
	 */

	public $default_value = [];

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'items'    => [],
			'selected' => []
		];
	}

	/**
	 * Display property html.
	 */

	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		// Override selected setting with
		// database value if not empty.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		// Selected setting need to be an array.
		if ( ! is_array( $settings->selected ) ) {
			$settings->selected = [$settings->selected];
		}

		foreach ( $settings->items as $key => $value ) {

			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			?>
			<input type="checkbox" value="<?php echo $value; ?>"
			       name="<?php echo $this->html_name(); ?>[]" <?php echo in_array( $value, $settings->selected ) ? 'checked="checked"' : ''; ?> />
			<?php
			echo $key . '<br />';
		}
	}

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
		if ( is_string( $value ) && ! papi_is_empty( $value ) ) {
			return [$value];
		}

		if ( ! is_array( $value ) ) {
			return $this->default_value;
		}

		return $value;
	}

}
