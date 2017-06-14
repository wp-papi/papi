<?php

/**
 * Base property that implements the rendering of
 * a property in WordPress admin.
 */
class Papi_Property extends Papi_Core_Property {

	/**
	 * Get value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		$value = $this->get_option( 'value' );

		if ( papi_is_empty( $value ) ) {
			$type        = papi_get_meta_type();
			$slug        = $this->get_slug( true );
			$value       = papi_get_field( $slug, null, $type );
			$post_status = get_post_status( $this->get_post_id() );

			if ( papi_is_empty( $value ) && ( $post_status === false || $post_status === 'auto-draft' ) ) {
				$value = $this->get_option( 'default' );
			}
		}

		if ( papi_is_empty( $value ) ) {
			return $this->default_value;
		}

		if ( $this->convert_type === 'string' ) {
			$value = papi_convert_to_string( $value );
		}

		return papi_santize_data( $value );
	}

	/**
	 * Render property html.
	 */
	public function html() {
	}

	/**
	 * Determine if the property can be rendered or not.
	 *
	 * @return bool
	 */
	public function can_render() {
		// Check if current user can view the property.
		if ( ! $this->current_user_can() ) {
			return false;
		}

		// A disabled property should not be rendered.
		if ( $this->disabled() ) {
			return false;
		}

		// Check language option, so we don't render properties on a different language.
		if ( $lang = $this->get_option( 'lang' ) ) {
			// Support array of langs.
			$lang = is_array( $lang ) ? $lang : [$lang];

			// Only render if it's the right language if it exist.
			return in_array( papi_get_lang(), $lang, true );
		}

		// If no valid lang query string exists we have to override the display property.
		return $this->get_option( 'lang' ) === false;
	}

	/**
	 * Render the property.
	 */
	public function render() {
		// Bail if we can't render the property.
		if ( ! $this->can_render() ) {
			return;
		}

		// Override display with rules check.
		if ( $this->display() ) {
			$this->display = $this->render_is_allowed_by_rules();
		}

		// Render property.
		$this->render_row_html();
		$this->render_hidden_html();
		$this->render_rules_json();
	}

	/**
	 * Render AJAX request.
	 */
	public function render_ajax_request() {
		papi_render_property( $this );
	}

	/**
	 * Render the property description.
	 */
	protected function render_description_html() {
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
	protected function render_hidden_html() {
		$slug = $this->get_option( 'slug' );

		if ( substr( $slug, - 1 ) === ']' ) {
			$slug = substr( $slug, 0, - 1 );
			$slug = papi_get_property_type_key( $slug );
			$slug .= ']';
		} else {
			$slug = papi_get_property_type_key( $slug );
		}

		$slug          = papify( $slug );
		$options       = $this->get_options();
		$property_json = base64_encode( papi_maybe_json_encode( $options ) );

		papi_render_html_tag( 'input', [
			'data-property' => strtolower( $this->get_option( 'type' ) ),
			'name'          => $slug,
			'type'          => 'hidden',
			'value'         => $property_json
		] );
	}

	/**
	 * Render label for the property.
	 */
	protected function render_label_html() {
		$title = $this->get_option( 'title' );

		papi_render_html_tag( 'label', [
			'for'   => $this->html_id(),
			'title' => trim( $title . ' ' . papi_property_require_text( $this->get_options() ) ),
			$title,
			papi_property_required_html( $this )
		] );
	}

	/**
	 * Render property html.
	 */
	protected function render_property_html() {
		papi_render_html_tag( 'div', [
			'class'         => 'papi-before-html ' . $this->get_option( 'before_class' ),
			'data-property' => $this->get_option( 'type' ),
			papi_maybe_get_callable_value( $this->get_option( 'before_html' ) )
		] );

		$this->html();

		papi_render_html_tag( 'div', [
			'class'         => 'papi-after-html ' . $this->get_option( 'after_class' ),
			'data-property' => $this->get_option( 'type' ),
			papi_maybe_get_callable_value( $this->get_option( 'after_html' ) )
		] );
	}

	/**
	 * Render the final html that is displayed in the table.
	 */
	protected function render_row_html() {
		$display_class = $this->display() ? '' : ' papi-hide';
		$rules_class   = papi_is_empty( $this->get_rules() ) ? '' : ' papi-rules-exists';
		$css_class     = trim( $display_class . $rules_class );
		$css_class    .= sprintf( ' papi-layout-%s', $this->get_option( 'layout' ) );

		if ( $this->get_option( 'raw' ) ) {
			echo sprintf( '<div class="%s">', esc_attr( $css_class ) );
			$this->render_property_html();
			echo '</div>';
		} else {
			?>
			<tr class="<?php echo esc_attr( $css_class ); ?>">
				<?php if ( $this->get_option( 'sidebar' ) && $this->get_option( 'layout' ) === 'horizontal' ): ?>
					<td class="papi-table-sidebar">
						<?php
							$this->render_label_html();
							$this->render_description_html();
						?>
					</td>
				<?php endif; ?>
				<td <?php echo $this->get_option( 'sidebar' ) && $this->get_option( 'layout' ) === 'horizontal' ? '' : 'colspan="2"'; ?>>
					<?php
					// Render vertical layout where title and description is above the property.
					if ( $this->get_option( 'layout' ) === 'vertical' && $this->get_option( 'sidebar' ) ) {
						$this->render_label_html();
						$this->render_description_html();
					}

					$this->render_property_html();
					?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Render Conditional rules as script tag with JSON.
	 */
	protected function render_rules_json() {
		$rules = $this->get_rules();

		if ( empty( $rules ) ) {
			return;
		}

		$rules = $this->conditional->prepare_rules( $rules, $this );

		papi_render_html_tag( 'script', [
			'data-papi-rule-source-slug' => $this->html_name(),
			'data-papi-rules'            => 'true',
			'type'                       => 'application/json',
			papi_maybe_json_encode( $rules )
		] );
	}
}
