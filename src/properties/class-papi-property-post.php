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

		if ( ! empty( $meta_key ) ) {
			$args = [
				'fields'         => 'ids',
				'meta_key'       => $meta_key,
				'meta_value'     => $value,
				'posts_per_page' => 1,
				'post_type'      => $this->get_setting( 'post_type' ),
			];

			$query = new WP_Query( $args );

			if ( ! empty( $query->posts ) ) {
				$value = $query->posts[0];
			}
		}

		$post = $this->default_value;

		// Allow only id to be returned.
		if ( ! papi_is_admin() && $this->get_setting( 'fields' ) === 'ids' ) {
			$post = $this->get_post_value( $value );
		} else if ( ! empty( $value ) ) {
			$post = get_post( $value );
		}

		if ( empty( $post ) ) {
			return $this->default_value;
		}

		if ( is_object( $post ) && empty( $post->ID ) ) {
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
			'allow_clear'   => true,
			'edit_url'      => true,
			'fields'        => '',
			'labels'        => [
				'select_post_type' => __( 'Select Post Type', 'papi' ),
				'select_item'      => __( 'Select %s', 'papi' )
			],
			'layout'        => 'single', // single or advanced
			'meta_key'      => '',
			'new_url'       => true,
			'placeholder'   => null,
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
			'post_status'            => 'any',
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
		} else if ( ! empty( $post ) ) {
			$post = get_post( $post );
			if ( $post instanceof WP_Post === false ) {
				return 0;
			}

			$post_id = $post->ID;
		}

		if ( empty( $post_id ) ) {
			return 0;
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
		$settings           = $this->get_settings();
		$layout             = $settings->layout;
		$labels             = $this->get_labels();
		$post_types         = $this->get_post_types();
		$render_label       = count( $post_types ) > 1;
		$advanced           = $render_label && $layout === 'advanced';
		$single             = $render_label && $layout !== 'advanced';
		$classes            = count( $post_types ) > 1 ? '' : 'papi-fullwidth';
		$value              = $this->get_value();
		$value              = $this->get_post_value( $value );
		$selected_label     = is_array( $labels ) && ! empty( $labels ) ? array_values( $labels )[0] : '';
		$selected_post_type = empty( $value ) ? '' : get_post_type( $value );
		$selected_post_type = empty( $selected_post_type ) ? '' : $selected_post_type;
		$posts              = $this->get_posts( $selected_post_type );

		if ( $settings->select2 ) {
			$classes .= ' papi-component-select2';
		}

		// When new url is true and we have more than one post type
		// we need to use the advanced layout so the placeholder option
		// will get the right post type in the new url.
		if ( $settings->new_url && $render_label ) {
			$advanced = true;
			$single = false;
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
								$selected = $post_type === $selected_post_type;

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
						<label for="<?php echo esc_attr( $this->html_id() ); ?>_posts">
							<?php echo esc_html( sprintf( $settings->labels['select_item'], $selected_label ) ); ?>
						</label>
					</td>
					<td>
						<?php endif; ?>

						<?php
							$placeholder = ! is_null( $settings->placeholder ) ? $settings->placeholder : '';
						?>

						<select
							class="<?php echo esc_attr( $classes ); ?>  papi-property-post-right"
							id="<?php echo esc_attr( $this->html_id() ); ?>_posts"
							name="<?php echo esc_attr( $this->html_name() ); ?>"
							data-allow-clear="<?php echo is_null( $settings->placeholder ) ? 'false' : 'true'; ?>"
							data-placeholder="<?php echo esc_attr( $placeholder ); ?>"
							data-width="100%"
						>

							<?php if ( ! empty( $settings->placeholder ) ): ?>
								<?php if ( $settings->new_url ): ?>
									<option data-placeholder data-new-url="<?php echo esc_attr( admin_url( 'post-new.php?post_type=' . $post_types[0] ) ); ?>"></option>
								<?php else: ?>
									<option></option>
								<?php endif; ?>
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
										'data-allow-clear' => $settings->allow_clear,
										'data-edit-url'    => $settings->edit_url ? get_edit_post_link( $post ) : '',
										'data-new-url'     => $settings->new_url ? admin_url( 'post-new.php?post_type=' . $post->post_type ) : '',
										'selected'         => $value === $this->get_post_value( $post ),
										'value'            => $this->get_post_value( $post ),
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
	 * Render option template.
	 */
	public function render_option_template() {
		$settings   = $this->get_settings();
		$post_types = $this->get_post_types();

		?>
		<script type="text/template" id="tmpl-papi-property-post-option">
			<option
				data-allow-clear="<?php echo esc_attr( $settings->allow_clear ); ?>"

				<?php if ( $settings->edit_url ): ?>
				data-edit-url="<?php echo esc_attr( admin_url( 'post.php' ) ); ?>?post=<%= id %>&action=edit"
				<?php endif; ?>

				<?php if ( $settings->new_url ): ?>
				data-new-url="<?php echo esc_attr( admin_url( 'post-new.php?post_type=' ) ); ?><%= typeof type !== 'undefined' ? type : '<?php echo esc_attr( $post_types[0] ); ?>' %>"
				<?php endif; ?>

				value="<%= id %>"
				>
				<%= title %>
			</option>
		</script>
		<script type="text/template" id="tmpl-papi-property-post-option-placeholder">
			<option
				data-placeholder

				<?php if ( $settings->new_url ): ?>
				data-new-url="<?php echo esc_attr( admin_url( 'post-new.php?post_type=' ) ); ?><%= typeof type !== 'undefined' ? type : '<?php echo esc_attr( $post_types[0] ); ?>' %>"
				<?php endif; ?>
				>
			</option>
		</script>
		<?php
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_head', [$this, 'render_option_template'] );
	}
}
