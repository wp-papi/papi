<?php

/**
 * Term property that handle relationship between
 * terms and posts.
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
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
			return $this->get_term( $value );
		}

		return $this->default_value;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'labels'        => [
				'select_taxonomy' => __( 'Select Taxonomy', 'papi' ),
				'select_item'      => __( 'Select %s term', 'papi' )
			],
			'layout'        => 'single', // Single or multiple
			'placeholder'   => '',
			'taxonomy'      => '',
			'select2'       => true,
			'query'         => []
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
	 * Get post types.
	 *
	 * @return array
	 */
	protected function get_taxonomies() {
		return papi_to_array( $this->get_setting( 'taxonomy' ) );
	}

	/**
	 * Get terms for specified taxonomy.
	 * @param  string $taxonomy
	 * @return array
	 */
	protected function get_terms( $taxonomy ) {
		// Prepare arguments for get_terms.
		$query  = $this->get_setting( 'query' );
		$args = array_merge( $query, [
			'fields' => 'id=>name'
		] );

		$terms = [];
		if ( taxonomy_exists( $taxonomy ) ) {
			$terms = get_terms( $taxonomy, $args );
		}

		 return $terms;
	}

	/**
	 * Get single term.
	 * @param  int $term_id
	 * @return object
	 */
	protected function get_term( $term_id ) {
		if ( version_compare( $GLOBALS['wp_version'], '4.4', '<' ) ) {
			$taxonomies = $this->get_taxonomies();
			$taxonomy = reset( $taxonomies );
			$term = get_term( $term_id, $taxonomy );
		}
		else {
			$term = get_term( $term_id );
		}

		return $term;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings                = $this->get_settings();
		$layout                  = $settings->layout;
		$labels                  = $this->get_labels();
		$taxonomies              = $this->get_taxonomies();
		$render_label            = count( $taxonomies ) > 1;
		$multiple                = $render_label && $layout === 'multiple';
		$single                  = $render_label && $layout !== 'multiple';
		$classes                 = count( $taxonomies ) > 1 ? '' : 'papi-fullwidth';
		$value                   = $this->get_value();
		$value                   = is_object( $value ) ? $value->term_id : 0;
		$selected_label          = reset( $labels );
		$selected_term           = $this->get_term( $value ) ? : '';
		$selected_term           = is_wp_error( $selected_term ) || empty( $selected_term ) ? '' : $selected_term;
		$selected_taxonomy       = empty( $selected_term ) ? reset( $taxonomies ) : $selected_term->taxonomy;

		if ( $settings->select2 ) {
			$classes = ' papi-component-select2';
		}

		?>

		<div class="papi-property-term <?php echo $multiple ? 'multiple' : ''; ?>">
			<?php if ( $multiple ): ?>
				<table class="papi-table">
					<tr>
						<td>
							<label for="<?php echo $this->html_id(); ?>_taxonomy">
							<?php echo $settings->labels['select_taxonomy']; ?>
							</label>
						</td>
						<td>
							<select
								id="<?php echo $this->html_id(); ?>_taxonomy"
								class="<?php echo $classes; ?> papi-property-term-left"
								data-select-item="<?php echo $settings->labels['select_item']; ?>"
								data-term-query='<?php echo papi_maybe_json_encode( $settings->query ); ?>'
								data-width="100%"
								>
								<?php
								foreach ( $labels as $taxonomy => $label ) {
									$selected = $taxonomy === $selected_taxonomy ? 'selected' : null;

									papi_render_html_tag( 'option', [
										'value'    => $taxonomy,
										'selected' => $selected,
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
						<label for="<?php echo $this->html_id(); ?>_terms">
							<?php echo sprintf( $settings->labels['select_item'], $selected_label ); ?>
						</label>
					</td>
					<td>
			<?php endif; ?>

			<select
				class="<?php echo $classes; ?>  papi-property-term-right"
				id="<?php echo $this->html_id(); ?>_terms"
				name="<?php echo $this->html_name(); ?>"
				class="<?php echo $classes; ?>"
				data-allow-clear="<?php echo ! empty( $settings->placeholder ); ?>"
				data-placeholder="<?php echo isset( $settings->placeholder ) ? $settings->placeholder : ''; ?>"
				data-width="100%">

				<?php if ( ! empty( $settings->placeholder ) ): ?>
					<option value=""></option>
				<?php endif; ?>

				<?php foreach ( $taxonomies as $taxonomy ) : ?>
					<?php
					if ( $multiple && $taxonomy !== $selected_taxonomy ) {
						continue;
					}

					$terms = $this->get_terms( $taxonomy );
					if ( empty( $terms ) ) {
						continue;
					}
					?>

					<?php if ( $single ): ?>
						<optgroup label="<?php echo $labels[$taxonomy]; ?>">
					<?php endif; ?>

					<?php
					foreach ( $terms as $term_id => $term_name ) {
						if ( papi_is_empty( $term_name ) ) {
							continue;
						}

						papi_render_html_tag( 'option', [
							'value'      => $term_id,
							'selected'   => $value === $term_id ? 'selected' : null,
							$term_name
						] );
					}
					?>

					<?php if ( $single ): ?>
						</optgroup>
					<?php endif; ?>

				<?php endforeach; ?>
			</select>

			<?php if ( $multiple ): ?>
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
