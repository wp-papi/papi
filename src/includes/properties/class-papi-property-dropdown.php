<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Dropdown.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Dropdown extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'items'    => array(),
			'selected' => array()
		);
	}

	/**
	 * Display property html.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options  = $this->get_options();
		$settings = $this->get_settings();
		$value    = $this->get_value();

		// Override selected setting with
		// database value if not empty.
		if ( ! papi_is_empty( $value ) ) {
			$settings->selected = $value;
		}

		?>
		<select class="papi-vendor-select2 papi-fullwidth" name="<?php echo $options->slug; ?>">
			<?php
			foreach ( $settings->items as $key => $value ):
				if ( is_numeric( $key ) ) {
					$key = $value;
				}
				?>
				<option
					value="<?php echo $value; ?>" <?php echo $value == $settings->selected ? 'selected="selected"' : ''; ?>><?php echo $key; ?></option>
			<?php endforeach; ?>
		</select>
	<?php
	}

}
