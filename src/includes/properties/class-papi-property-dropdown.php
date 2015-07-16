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
			'selected'    => [],
			'select2'     => true
		];
	}

	/**
	 * Get dropdown items.
	 *
	 * @return array
	 */

	protected function get_items() {
		return papi_to_array( $this->get_setting( 'items', [] ) );
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

		$classes = 'papi-fullwidth';

		if ( $settings->select2 ) {
			$classes = ' papi-component-select2';
		}
		?>
		<select
			class="<?php echo $classes; ?>"
			id="<?php echo $this->html_id(); ?>"
			name="<?php echo $this->html_name(); ?>"
			data-allow-clear="true"
			data-placeholder="<?php echo $settings->placeholder; ?>"
			data-width="100%">

			<?php if ( ! empty( $settings->placeholder ) ): ?>
				<option value=""></option>
			<?php endif; ?>

			<?php foreach ( $this->get_items() as $key => $value ):
				if ( is_numeric( $key ) ) {
					$key = $value;
				}

				if ( papi_is_empty( $key ) ) {
					continue;
				}
				?>
				<option
					value="<?php echo $value; ?>" <?php echo $value === $settings->selected ? 'selected="selected"' : ''; ?>><?php echo $key; ?></option>
			<?php endforeach; ?>
		</select>
	<?php
	}

}
