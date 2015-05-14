<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Text.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Text extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'allow_html' => false
		];
	}

	/**
	 * Display property html.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$value    = $this->get_value();
		?>
		<textarea name="<?php echo $this->html_name(); ?>"
		          class="papi-property-text"><?php echo sanitize_text_field( $value ); ?></textarea>
		<?php
	}
}
