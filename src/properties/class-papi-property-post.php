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
		$meta_key = $this->get_setting( 'meta_key' );

		if ( empty( $meta_key ) ) {
			if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
				$post_id = $value;
			}
		} else {
			$args = [
				'fields'         => 'ids',
				'meta_key'       => $meta_key,
				'meta_value'     => $value,
				'posts_per_page' => 1,
				'post_type'      => $this->get_setting( 'post_type' ),
			];

			$query = new WP_Query( $args );

			if ( ! empty( $query->posts ) ) {
				$post_id = $query->posts[0];
			}
		}

		// Allow only id to be returned.
		if ( ! papi_is_admin() && $this->get_setting( 'fields' ) === 'ids' ) {
			$post = $this->get_post_value( $post_id );
		} elseif ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );
		}

		if ( empty( $post ) ) {
			return $this->default_value;
		}

		return $post;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'fields'        => '',
			'labels'        => [
				'select_post_type' => __( 'Select Post Type', 'papi' ),
				'select_item'      => __( 'Select %s', 'papi' )
			],
			'layout'        => 'single', // single or advanced
			'meta_key'      => '',
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
			if ( post_type_exists( $post_type ) ) {
				$post_type_object    = get_post_type_object( $post_type );
				$results[$post_type] = $post_type_object->labels->menu_name;
			}
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

		$posts = ( new WP_Query( $args ) )->posts;

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
	 * Get matching value based on key from a post.
	 *
	 * @param  mixed $post
	 *
	 * @return mixed
	 */
	protected function get_post_value( $post ) {
		$meta_key = $this->get_setting( 'meta_key' );

		if ( is_numeric( $post ) ) {
			$post_id = $post;
		} else {
			$post = get_post( $post );

			if ( $post instanceof WP_Post === false ) {
				return 0;
			}

			$post_id = $post->ID;
		}

		if ( ! empty( $meta_key ) ) {
			$value = get_post_meta( $post_id, $meta_key, true );
		} else {
			$value = $post_id;
		}

		return $value;
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
		$value              = $this->get_post_value( $value );
		$selected_label     = array_shift( $labels );
		$selected_post_type = get_post_type( $value ) ? : '';
		$posts              = $this->get_posts( $selected_post_type );

		if ( $settings->select2 ) {
			$classes .= ' papi-component-select2';
		}
		?>

		<div class="papi-property-post <?php echo $advanced ? 'advanced' : ''; ?>">
			<?php if ( $advanced ): ?>
			<table class="papi-table">
				<tr>
					<td>
						<label for="<?php echo esc_attr( $this->html_id() ); ?>_post_type">
							<?php echo esc_html( $settings->labels['select_post_type'] ); ?>
						</label>
					</td>
					<td>
						<select
							id="<?php echo esc_attr( $this->html_id() ); ?>_post_type"
							class="<?php echo esc_attr( $classes ); ?> papi-property-post-left"
							data-select-item="<?php echo esc_attr( $settings->labels['select_item'] ); ?>"
							data-post-query='<?php echo esc_attr( papi_maybe_json_encode( $settings->query ) ); ?>'
							data-width="100%"
						>
							<?php
							foreach ( $labels as $post_type => $label ) {
								papi_render_html_tag( 'option', [
									'value'    => $post_type,
									'selected' => $post_type === $selected_post_type,
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
						<label for="<?php echo esc_attr( $this->html_id() ); ?>_posts">
							<?php echo esc_html( sprintf( $settings->labels['select_item'], $selected_label ) ); ?>
						</label>
					</td>
					<td>
						<?php endif; ?>

						<select
							class="<?php echo esc_attr( $classes ); ?>  papi-property-post-right"
							id="<?php echo esc_attr( $this->html_id() ); ?>_posts"
							name="<?php echo esc_attr( $this->html_name() ); ?>"
							data-allow-clear="<?php echo empty( $settings->placeholder ) ? 'false' : 'true'; ?>"
							data-placeholder="<?php echo esc_attr( $settings->placeholder ); ?>"
							data-width="100%"
						>

							<?php if ( ! empty( $settings->placeholder ) ): ?>
								<option value=""></option>
							<?php endif; ?>

							<?php foreach ( $posts as $label => $items ) : ?>

								<?php if ( $single ): ?>
									<optgroup label="<?php echo esc_attr( $label ); ?>">
								<?php endif; ?>

								<?php
								foreach ( $items as $post ) {
									if ( papi_is_empty( $post->post_title ) ) {
										continue;
									}

									papi_render_html_tag( 'option', [
										'data-edit-url' => get_edit_post_link( $post ),
										'selected'      => $value === $this->get_post_value( $post ),
										'value'         => $this->get_post_value( $post ),
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
