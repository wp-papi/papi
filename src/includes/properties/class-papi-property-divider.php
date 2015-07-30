<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Divider class.
 *
 * @package Papi
 */
class Papi_Property_Divider extends Papi_Property {

	/**
	 * Display property html.
	 */
	public function html() {
		$options = $this->get_options();
		?>
		<div class="papi-property-divider">
			<h3>
				<span><?php echo $options->title; ?></span>
			</h3>

			<?php if ( ! papi_is_empty( $options->description ) ): ?>
				<p><?php echo $options->description; ?></p>
			<?php endif; ?>
		</div>
	<?php
	}

	/**
	 * Render the final html that is displayed in the table.
	 */
	public function render_row_html() {
		if ( ! $this->get_option( 'raw' ) ):
			?>
			<tr class="<?php echo $this->display ? '' : 'papi-hide'; ?>">
				<td colspan="2">
					<?php $this->html(); ?>
				</td>
			</tr>
		<?php
		else:
			parent::render_row_html();
		endif;
	}

}
