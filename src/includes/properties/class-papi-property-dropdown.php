<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Dropdown.
 *
 * @package Papi
 */

class Papi_Property_Dropdown extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'placeholder' => '',
			'items'       => [],
			'selected'    => []
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

		?>
		<select
			class="papi-component-select2 papi-fullwidth"
			name="<?php echo $this->html_name(); ?>"
			data-allow-clear="true"
			data-placeholder="<?php echo $settings->placeholder; ?>"
			data-width="100%">

			<option value=""></option>

			<?php foreach ( $settings->items as $key => $value ):
				if ( is_numeric( $key ) ) {
					$key = $value;
				}

				if ( papi_is_empty( $key ) ) {
					continue;
				}
				?>
				<option
					value="<?php echo $value; ?>" <?php echo $key == $settings->selected ? 'selected="selected"' : ''; ?>><?php echo $key; ?></option>
			<?php endforeach; ?>
		</select>
	<?php
	}

}
