<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Property Color
 *
 * @package Papi
 */

class Papi_Property_Color extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'palettes'   => [],
			'show_input' => true
		];
	}

	/**
	 * Display property html.
	 */

	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();
		?>
			<div class="papi-property-color-picker">
				<input type="<?php echo $settings->show_input === true ? 'text' : 'hidden'; ?>"
				value="<?php echo $value; ?>" data-palettes='<?php echo json_encode( $settings->palettes ); ?>'
				name="<?php echo $this->html_name(); ?>" />
			</div>
		<?php
	}
}
