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
	 * Display the html to display from the property.
	 */

	public function html() {
	}

	/**
	 * Get the html id attribute value.
	 *
	 * @param object|string $suffix
	 * @param int $row
	 *
	 * @return string
	 */

	public function html_id( $suffix = '', $row = null ) {
		if ( is_array( $suffix ) || is_object( $suffix ) ) {
			return '_' . $this->html_name( $suffix, $row );
		} else {
			$suffix = empty( $suffix ) || ! is_string( $suffix ) ? '' : '_' . $suffix;
			$suffix = papi_underscorify( papi_slugify( $suffix ) );
		}

		return sprintf( '_%s%s', $this->html_name(), $suffix );
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

		if ( $render && $this->get_option( 'disabled' ) === false ) {
			$this->render_row_html();
			$this->render_hidden_html();
		}
	}

	/**
	 * Render the property description.
	 */

	public function render_description_html() {
		if ( papi_is_empty( $this->get_option( 'description' ) ) ) {
			return;
		}

		papi_render_html_tag( 'p', [
			papi_nl2br( $this->get_option( 'description' ) )
		] );
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

		papi_render_html_tag( 'input', [
			'data-property' => $this->get_option( 'type' ),
			'name'          => $slug,
			'type'          => 'hidden',
			'value'         => $property_serialized
		] );
	}

	/**
	 * Get label for the property.
	 */

	public function render_label_html() {
		$title = $this->get_option( 'title' );

		papi_render_html_tag( 'label', [
			'for'   => $this->html_id(),
			'title' => trim( $title . ' ' . papi_require_text( $this->get_options() ) ),
			$title,
			papi_required_html( $this->get_options() )
		] );
	}

	/**
	 * Render the final html that is displayed in the table.
	 */

	public function render_row_html() {
		if ( ! $this->get_option( 'raw' ) ):
			?>
			<tr class="<?php echo $this->display ? '' : 'papi-hide'; ?>">
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
			if ( ! $this->display ):
				echo '<div class="papi-hide">';
			endif;

			$this->html();

			if ( ! $this->display ):
				echo '</div>';
			endif;
		endif;
	}
}
