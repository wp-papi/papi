<?php

/**
 * Term property that populates a dropdown.
 */
class Papi_Property_Term extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'object';

	/**
	 * Format the value of the property before it's returned to the application.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $term_id
	 *
	 * @return array
	 */
	public function format_value( $value, $slug, $term_id ) {
		$meta_key = $this->get_setting( 'meta_key' );

		if ( empty( $meta_key ) ) {
			if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
				$term_id = $value;
			}
		} else {
			$args = [
				'fields'     => 'ids',
				'meta_key'   => $meta_key,
				'meta_value' => $value,
				'hide_empty' => false,
				'taxonomy'   => $this->get_setting( 'taxonomy' ),
				'number'     => 1
			];

			$terms = get_terms( $args );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$term_id = $terms[0];
			}
		}

		// Allow only id to be returned.
		if ( ! papi_is_admin() && $this->get_setting( 'fields' ) === 'ids' ) {
			$term = $this->get_term_value( $term_id );
		} elseif ( ! empty( $term_id ) ) {
			$term = get_term( $term_id );
		}

		if ( empty( $term ) || is_wp_error( $term ) ) {
			$term = $this->default_value;
		}

		return $term;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'fields'      => '',
			'labels'      => [
				'select_taxonomy' => __( 'Select Taxonomy', 'papi' ),
				'select_item'     => __( 'Select %s term', 'papi' )
			],
			'layout'      => 'single', // Single or advanced
			'meta_key'    => '',
			'placeholder' => '',
			'taxonomy'    => '',
			'select2'     => true,
			'query'       => []
		];
	}

	/**
	 * Get labels from taxonomies.
	 *
	 * @return array
	 */
	protected function get_labels() {
		$results = [];

		foreach ( $this->get_taxonomies() as $taxonomy ) {
			if ( taxonomy_exists( $taxonomy ) ) {
				$taxonomy_object    = get_taxonomy( $taxonomy );
				$results[$taxonomy] = $taxonomy_object->labels->name;
			}
		}

		return $results;
	}

	/**
	 * Get taxonomies.
	 *
	 * @return array
	 */
	protected function get_taxonomies() {
		return papi_to_array( $this->get_setting( 'taxonomy' ) );
	}

	/**
	 * Get terms for specified taxonomy.
	 *
	 * @param  string $taxonomy
	 *
	 * @return array
	 */
	protected function get_terms( $taxonomy ) {
		// Prepare arguments for get_terms.
		$query = $this->get_setting( 'query' );
		$args  = array_merge( $query, [
			'fields' => 'id=>name'
		] );

		$terms = [];
		if ( taxonomy_exists( $taxonomy ) ) {
			$terms = get_terms( $taxonomy, $args );
		}

		return $terms;
	}

	/**
	 * Get matching value based on key from a term.
	 *
	 * @param  mixed $term
	 *
	 * @return mixed
	 */
	protected function get_term_value( $term ) {
		$meta_key = $this->get_setting( 'meta_key' );

		if ( is_numeric( $term ) ) {
			$term_id = $term;
		} else {
			$term = get_term( $term );

			if ( $term instanceof WP_Term === false ) {
				return 0;
			}

			$term_id = $term->term_id;
		}

		if ( ! empty( $meta_key ) ) {
			$value = get_term_meta( $term_id, $meta_key, true );
		} else {
			$value = $term_id;
		}

		return $value;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings          = $this->get_settings();
		$layout            = $settings->layout;
		$labels            = $this->get_labels();
		$taxonomies        = $this->get_taxonomies();
		$render_label      = count( $taxonomies ) > 1;
		$advanced          = $render_label && $layout === 'advanced';
		$single            = $render_label && $layout !== 'advanced';
		$classes           = count( $taxonomies ) > 1 ? '' : 'papi-fullwidth';
		$value             = $this->get_value();
		$selected_term     = get_term( $value );
		$selected_term     = is_wp_error( $selected_term ) || empty( $selected_term ) ? '' : $selected_term;
		$selected_taxonomy = empty( $selected_term ) ? reset( $taxonomies ) : $selected_term->taxonomy;
		$value             = $this->get_term_value( $value );
		$selected_label    = reset( $labels );

		if ( $settings->select2 ) {
			$classes = ' papi-component-select2';
		}

		?>

		<div class="papi-property-term <?php echo $advanced ? 'advanced' : ''; ?>">
			<?php if ( $advanced ): ?>
				<table class="papi-table">
					<tr>
						<td>
							<label for="<?php echo esc_attr( $this->html_id() ); ?>_taxonomy">
								<?php echo esc_html( $settings->labels['select_taxonomy'] ); ?>
							</label>
						</td>
						<td>
							<select
								id="<?php echo esc_attr( $this->html_id() ); ?>_taxonomy"
								class="<?php echo esc_attr( $classes ); ?> papi-property-term-left"
								data-select-item="<?php echo esc_attr( $settings->labels['select_item'] ); ?>"
								data-term-query='<?php echo esc_attr( papi_maybe_json_encode( $settings->query ) ); ?>'
								data-width="100%"
								>
								<?php
								foreach ( $labels as $taxonomy => $label ) {
									papi_render_html_tag( 'option', [
										'value'    => $taxonomy,
										'selected' => $taxonomy === $selected_taxonomy,
										$label
									] );

									if ( $selected ) {
										$selected_label = $label;
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="<?php echo esc_attr( $this->html_id() ); ?>_terms">
								<?php echo esc_html( sprintf( $settings->labels['select_item'], $selected_label ) ); ?>
							</label>
						</td>
					<td>
			<?php endif; ?>

			<select
				class="<?php echo esc_attr( $classes ); ?>  papi-property-term-right"
				id="<?php echo esc_attr( $this->html_id() ); ?>_terms"
				name="<?php echo esc_attr( $this->html_name() ); ?>"
				class="<?php echo esc_attr( $classes ); ?>"
				data-allow-clear="<?php echo empty( $settings->placeholder ) ? 'false' : 'true'; ?>"
				data-placeholder="<?php echo esc_attr( isset( $settings->placeholder ) ? $settings->placeholder : '' ); ?>"
				data-width="100%">

				<?php if ( ! empty( $settings->placeholder ) ): ?>
					<option value=""></option>
				<?php endif; ?>

				<?php foreach ( $taxonomies as $taxonomy ) : ?>
					<?php
					if ( $advanced && $taxonomy !== $selected_taxonomy ) {
						continue;
					}

					$terms = $this->get_terms( $taxonomy );
					if ( empty( $terms ) ) {
						continue;
					}
					?>

					<?php if ( $single ): ?>
						<optgroup label="<?php echo esc_attr( $labels[$taxonomy] ); ?>">
					<?php endif; ?>

					<?php
					foreach ( $terms as $term_id => $term_name ) {
						if ( papi_is_empty( $term_name ) ) {
							continue;
						}

						papi_render_html_tag( 'option', [
							'value'    => $this->get_term_value( $term_id ),
							'selected' => $value === $this->get_term_value( $term_id ),
							esc_html( $term_name )
						] );
					}
					?>

					<?php if ( $single ): ?>
						</optgroup>
					<?php endif; ?>

				<?php endforeach; ?>
			</select>

			<?php if ( $advanced ): ?>
					</td>
				</tr>
			</table>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Import value to the property.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( is_object( $value ) && isset( $value->term_id ) ) {
			return $value->term_id;
		}

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		return $this->default_value;
	}
}
