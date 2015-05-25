<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property class.
 *
 * @package Papi
 */

class Papi_Property extends Papi_Core_Property {

	/**
	 * Get default options.
	 *
	 * @TODO check if this can be removed,
	 * papi ajax is using it.
	 *
	 * @return array
	 */

	public static function default_options() {
		$property = new static;
		$default_options = $property->default_options;

		if ( $default_options['sort_order'] === -1 ) {
			$default_options['sort_order'] = papi_filter_settings_sort_order();
		}

		return $default_options;
	}

	/**
	 * Get the html to display from the property.
	 */

	public function html() {
	}

	/**
	 * Get html name for property with or without sub property and row number.
	 *
	 * @param array|object $sub_property
	 * @param int $row
	 *
	 * @return string
	 */

	public function html_name( $sub_property = null, $row = null ) {
		$base_slug = $this->get_option( 'slug' );

		if ( is_null( $sub_property ) ) {
			return $base_slug;
		}

		if ( is_numeric( $row ) ) {
			$base_slug = sprintf( '%s[%d]', $base_slug, intval( $row ) );
		}

		if ( ! ( $sub_property instanceof Papi_Property ) ) {
			if ( is_array( $sub_property ) || is_object( $sub_property ) ) {
				$sub_property = self::factory( $sub_property );
			} else {
				return $base_slug;
			}
		}

		return sprintf( '%s[%s]', $base_slug, papi_remove_papi( $sub_property->get_option( 'slug' ) ) );
	}

	/**
	 * Render the property description.
	 */

	public function render_description_html() {
		if ( papi_is_empty( $this->get_option( 'description' )  ) ) {
			return;
		}

		?>
		<p><?php echo papi_nl2br( $this->get_option( 'description' ) ); ?></p>
	<?php
	}

	/**
	 * Output hidden input field that cointains which property is used.
	 */

	public function render_hidden_html() {
		$slug = $this->get_option( 'slug' );

		if ( substr( $slug, - 1 ) === ']' ) {
			$slug = substr( $slug, 0, - 1 );
			$slug = papi_get_property_type_key( $slug );
			$slug .= ']';
		} else {
			$slug = papi_get_property_type_key( $slug );
		}

		$slug = papify( $slug );

		$options = $this->get_options();
		$property_serialized = base64_encode( serialize( $options ) );

		?>
		<input type="hidden" value="<?php echo $property_serialized; ?>" name="<?php echo $slug; ?>"  data-property="<?php echo $this->get_option( 'type' ); ?>" />
	<?php
	}

	/**
	 * Get label for the property.
	 */

	public function render_label_html() {
		$title = $this->get_option( 'title' );
		?>
		<label for="<?php echo $this->get_option( 'slug' ); ?>" title="<?php echo $title . ' ' . papi_require_text( $this->get_options() ); ?>">
			<?php
			echo $title;
			echo papi_required_html( $this->get_options() );
			?>
		</label>
	<?php
	}

	/**
	 * Render the final html that is displayed in the table.
	 */

	public function render_row_html() {
		if ( ! $this->get_option( 'raw' ) ):
			?>
			<tr>
				<?php if ( $this->get_option( 'sidebar' ) ): ?>
					<td>
						<?php
							$this->render_label_html();
							$this->render_description_html();
						?>
					</td>
				<?php endif; ?>
				<td <?php echo $this->get_option( 'sidebar' ) ? '' : 'colspan="2"'; ?>>
					<?php $this->html(); ?>
				</td>
			</tr>
		<?php
		else :
			$this->html();
		endif;
	}
}
