<?php

/**
 * Post property that can handle relationship
 * between posts.
 */
class Papi_Property_Post extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'object';

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
			return get_post( $value );
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
				'select_post_type' => __( 'Select Post Type', 'papi' ),
				'select_item'      => __( 'Select %s', 'papi' )
			],
			'layout'        => 'single', // single or advanced
			'placeholder'   => '',
			'post_type'     => 'post',
			'select2'       => true,
			'query'         => []
		];
	}

	/**
	 * Get labels from post types.
	 *
	 * @return array
	 */
	protected function get_labels() {
		$results = [];

		foreach ( $this->get_post_types() as $post_type ) {
			$post_type_object    = get_post_type_object( $post_type );
			$results[$post_type] = $post_type_object->labels->menu_name;
		}

		return $results;
	}

	/**
	 * Get post types.
	 *
	 * @return array
	 */
	protected function get_post_types() {
		return papi_to_array( $this->get_setting( 'post_type' ) );
	}

	/**
	 * Get posts.
	 *
	 * @param  string $post_type
	 *
	 * @return array
	 */
	protected function get_posts( $post_type = '' ) {
		$query  = $this->get_setting( 'query' );
		$layout = $this->get_setting( 'layout' );

		// By default we add posts per page key with the value -1 (all).
		if ( ! isset( $query['posts_per_page'] ) ) {
			$query['posts_per_page'] = -1;
		}

		if ( $layout !== 'advanced' ) {
			$post_type = $this->get_post_types();
		} else if ( empty( $post_type ) ) {
			$post_type = $this->get_post_types();
			$post_type = array_shift( $post_type );
		}

		// Prepare arguments for WP_Query.
		$args = array_merge( $query, [
			'post_type'              => $post_type,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		] );

		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		// Keep only objects.
		$posts   = papi_get_only_objects( $posts );
		$results = [];

		foreach ( $posts as $post ) {
			$obj = get_post_type_object( $post->post_type );

			if ( empty( $obj ) ) {
				continue;
			}

			if ( ! isset( $results[$obj->labels->menu_name] ) ) {
				$results[$obj->labels->menu_name] = [];
			}

			$results[$obj->labels->menu_name][] = $post;
		}

		ksort( $results );

		return $results;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$layout             = $this->get_setting( 'layout' );
		$labels             = $this->get_labels();
		$post_types         = $this->get_post_types();
		$render_label       = count( $post_types ) > 1;
		$advanced           = $render_label && $layout === 'advanced';
		$single             = $render_label && $layout !== 'advanced';
		$classes            = count( $post_types ) > 1 ? '' : 'papi-fullwidth';
		$settings           = $this->get_settings();
		$value              = $this->get_value();
		$value              = is_object( $value ) ? $value->ID : 0;
		$selected_label     = array_shift( $labels );
		$selected_post_type = get_post_type( $value ) ? : '';
		$posts              = $this->get_posts( $selected_post_type );

		if ( $settings->select2 ) {
			$classes = ' papi-component-select2';
		}
		?>

		<div class="papi-property-post <?php echo $advanced ? 'advanced' : ''; ?>">
			<?php if ( $advanced ): ?>
				<table class="papi-table">
					<tr>
						<td>
							<label for="<?php echo $this->html_id(); ?>_post_type">
							<?php echo $settings->labels['select_post_type']; ?>
							</label>
						</td>
						<td>
							<select
								id="<?php echo $this->html_id(); ?>_post_type"
								class="<?php echo $classes; ?> papi-property-post-left"
								data-select-item="<?php echo $settings->labels['select_item']; ?>"
								data-post-query='<?php echo papi_maybe_json_encode( $settings->query ); ?>'
								data-width="100%"
								>
								<?php
								foreach ( $labels as $post_type => $label ) {
									$selected = $post_type === $selected_post_type ? 'selected' : null;

									papi_render_html_tag( 'option', [
										'value'    => $post_type,
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
						<label for="<?php echo $this->html_id(); ?>_posts">
							<?php echo sprintf( $settings->labels['select_item'], $selected_label ); ?>
						</label>
					</td>
					<td>
			<?php endif; ?>

			<select
				class="<?php echo $classes; ?>  papi-property-post-right"
				id="<?php echo $this->html_id(); ?>_posts"
				name="<?php echo $this->html_name(); ?>"
				data-allow-clear="<?php echo ! empty( $settings->placeholder ); ?>"
				data-placeholder="<?php echo $settings->placeholder; ?>"
				data-width="100%"
				>

				<?php if ( ! empty( $settings->placeholder ) ): ?>
					<option value=""></option>
				<?php endif; ?>

				<?php foreach ( $posts as $label => $items ) : ?>

					<?php if ( $single ): ?>
						<optgroup label="<?php echo $label; ?>">
					<?php endif; ?>

					<?php
					foreach ( $items as $post ) {
						if ( papi_is_empty( $post->post_title ) ) {
							continue;
						}

						papi_render_html_tag( 'option', [
							'value'      => $post->ID,
							'selected'   => $value === $post->ID ? 'selected' : null,
							$post->post_title
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
		if ( $value instanceof WP_Post ) {
			return $value->ID;
		}

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		return $this->default_value;
	}
}
