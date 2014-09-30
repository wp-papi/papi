<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property String
 *
 * @package Papi
 * @version 1.0.0
 */
class PropertyString extends Papi_Property {

	/**
	 * The input type to use.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $input_type = 'text';

	/**
	 * The default value.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $default_value = '';

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		// Property options.
		$options = $this->get_options();

		// Database value.
		$value = $this->get_value( $this->default_value );

		?>
		<input type="<?php echo $this->input_type; ?>" name="<?php echo $options->slug; ?>"
		       value="<?php echo $value; ?>" class="<?php echo $this->css_classes(); ?>"/>
	<?php
	}

}
