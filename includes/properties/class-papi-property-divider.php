<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Divider.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Divider extends Papi_Property {

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options = $this->get_options();
		?>
		<h3 class="hndle papi-property-divider">
			<span><?php echo $options->title; ?></span>
		</h3>

		<?php if ( ! empty( $options->instruction ) ): ?>
			<p><?php echo $options->instruction; ?></p>
		<?php endif; ?>
	<?php
	}

	/**
	 * Render the final html that is displayed in a table.
	 *
	 * @since 1.0.0
	 */

	public function render() {
		?>
		<tr class="papi-fullwidth">
			<td colspan="2">
				<?php $this->html(); ?>
			</td>
		</tr>
	<?php
	}

}
