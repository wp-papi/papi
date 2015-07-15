<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property String.
 *
 * @package Papi
 */

class Papi_Property_String extends Papi_Property {

	/**
	 * The input type to use.
	 *
	 * @var string
	 */

	public $input_type = 'text';

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
		$value   = $this->get_value();
		?>
		<input
			type="<?php echo $this->input_type; ?>"
			id="<?php echo $this->html_id(); ?>"
			name="<?php echo $this->html_name(); ?>"
			value="<?php echo $value; ?>" />
	<?php
	}

}
