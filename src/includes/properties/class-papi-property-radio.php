<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Radio.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Radio extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'items'    => [],
			'selected' => ''
		];
	}

	/**
	 * Display property html.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		// Override selected setting with
		// database value if not null.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		foreach ( $settings->items as $key => $value ) {

			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			?>
			<input type="radio" value="<?php echo $value ?>"
			       name="<?php echo $this->html_name(); ?>" <?php echo $value == $settings->selected ? 'checked="checked"' : ''; ?> />
			<?php
			echo $key . '<br />';
		}
	}
}
