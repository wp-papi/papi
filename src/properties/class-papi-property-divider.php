<?php

/**
 * Divider property.
 */
class Papi_Property_Divider extends Papi_Property {

	/**
	 * Determine if the property require a slug or not.
	 *
	 * @var bool
	 */
	protected $slug_required = false;

	/**
	 * Render property html.
	 */
	public function html() {
		$options = $this->get_options();
		$title   = '';
		$text    = '';

		if ( ! papi_is_empty( $options->title ) ) {
			$title = sprintf( '<h3><span>%s</span></h3>', esc_html( $options->title ) );
		}

		if ( ! papi_is_empty( $options->description ) ) {
			$text = sprintf( '<p>%s</p>', $options->description );
		}

		papi_render_html_tag( 'div', [
			'class'          => 'papi-property-divider',
			'data-papi-rule' => esc_attr( $this->html_name() ),
			sprintf( '%s%s', $title, $text )
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
