<?php

/**
 * Divider property.
 */
class Papi_Property_Divider extends Papi_Property {

	/**
	 * Render property html.
	 */
	public function html() {
		$options = $this->get_options();
		$text    = '';

		if ( ! papi_is_empty( $options->description ) ) {
			$text = sprintf( '<p>%s</p>', $options->description );
		}

		papi_render_html_tag( 'div', [
			'class'          => 'papi-property-divider',
			'data-papi-rule' => $this->html_name(),
			sprintf( '<h3><span>%s</span></h3>%s', $options->title, $text )
		] );
	}

	/**
	 * Render the final html that is displayed in the table.
	 */
	protected function render_row_html() {
		if ( $this->get_option( 'raw' ) ) {
			parent::render_row_html();
		} else {
			?>
			<tr class="<?php echo $this->display ? '' : 'papi-hide'; ?>">
				<td colspan="2">
					<?php $this->render_property_html(); ?>
				</td>
			</tr>
			<?php
		}
	}
}
