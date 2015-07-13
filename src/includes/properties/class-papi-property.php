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

		if ( ! papi_is_property( $sub_property ) ) {
			if ( is_array( $sub_property ) || is_object( $sub_property ) ) {
				$sub_property = self::factory( $sub_property );
			} else {
				return $base_slug;
			}
		}

		return sprintf( '%s[%s]', $base_slug, papi_remove_papi( $sub_property->get_slug() ) );
	}

	/**
	 * Render the property.
	 */

	public function render() {
		// Check so the property has a type and capabilities on the property.
		if ( ! papi_current_user_is_allowed( $this->get_option( 'capabilities' ) ) ) {
			return;
		}

		// Only render if it's the right language if the definition exist.
		if ( $this->get_option( 'lang' ) === strtolower( papi_get_qs( 'lang' ) ) ) {
			$render = true;
		} else {
			$render = $this->get_option( 'lang' ) === false && papi_is_empty( papi_get_qs( 'lang' ) );
		}

		$render = $this->get_option( 'disabled' ) === false;

		if ( ! $render ) {
			return;
		}

		if ( $this->display ) {
			$this->display = $this->render_is_allowed_by_rules();
		}

		$this->render_row_html();
		$this->render_hidden_html();
		$this->render_rules_json();
	}

	/**
	 * Render the property description.
	 */

	public function render_description_html() {
		if ( papi_is_empty( $this->get_option( 'description' ) ) ) {
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
		$display_class = $this->display ? '' : ' papi-hide';
		$rules_class   = papi_is_empty( $this->get_rules() ) ? '' : ' papi-rules-exists';
		$css_class     = $display_class . $rules_class;

		if ( ! $this->get_option( 'raw' ) ):
			?>
			<tr class="<?php echo $css_class; ?>">
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
		else:
			echo sprintf( '<div class="%s">', $css_class );
			$this->html();
			echo '</div>';
		endif;
	}

	/**
	 * Render Conditional rules as JSON.
	 */

	public function render_rules_json() {
		$rules = $this->get_rules();

		if ( empty( $rules ) ) {
			return;
		}

		$rules = $this->conditional->prepare_rules( $rules );
		?>
		<script type="application/json" data-papi-rules="true" data-papi-slug="<?php echo $this->html_name(); ?>">
			<?php echo json_encode( $rules ); ?>
		</script>
		<?php
	}
}
