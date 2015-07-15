<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Text.
 *
 * @package Papi
 */

class Papi_Property_Text extends Papi_Property {

	/**
	 * Get default settings.
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
	 */

	public function html() {
		$value = $this->get_value();
		?>
		<textarea id="<?php echo $this->html_id(); ?>"
				  name="<?php echo $this->html_name(); ?>"
		          class="papi-property-text"><?php echo sanitize_text_field( $value ); ?></textarea>
		<?php
	}
}
