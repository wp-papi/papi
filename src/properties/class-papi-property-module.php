<?php

/**
 * Module property that can handle relationship
 * between modules.
 */
class Papi_Property_Module extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'object';

	/**
	 * Default value.
	 *
	 * @var array
	 */
	public $default_value = [
		'module'   => null,
		'template' => null
	];

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
			$value = [
				'module'   => get_post( $value ),
				'template' => papi_data_get( $post_id, sprintf( '%s_template', unpapify( $this->html_name() ) ) )
			];

			if ( papi_is_admin() ) {
				return (object) $value;
			}

			// Return the template value instead of index when not in admin.
			if ( $value['module'] instanceof WP_Post ) {
				$templates = $this->get_templates( $value['module']->ID );

				// Check if index exists.
				if ( isset( $templates[$value['template']] ) ) {
					$value['template'] = $templates[$value['template']];
				}

				// Supports label and template array.
				if ( is_array( $value['template'] ) && isset( $value['template']['template'] ) ) {
					$value['template'] = $value['template']['template'];
				}
			}

			return (object) $value;
		}

		return (object) $this->default_value;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'edit_url'      => true,
			'labels'        => [
				'select_template' => __( 'Select Template', 'papi' ),
				'select_module'   => __( 'Select Module', 'papi' )
			],
			'new_url'       => true,
			'placeholder'   => '',
			'post_type'     => 'module',
			'select2'       => true,
			'query'         => [],
		];
	}

	/**
	 * Get post types.
	 *
	 * @return array
	 */
	protected function get_post_type() {
		$arr = papi_to_array( $this->get_setting( 'post_type' ) );
		return array_shift( $arr );
	}

	/**
	 * Get posts.
	 *
	 * @param  mixed $post_type
	 *
	 * @return array
	 */
	protected function get_posts( $post_type = null ) {
		$query = $this->get_setting( 'query' );

		// By default we add posts per page key with the value -1 (all).
		if ( ! isset( $query['posts_per_page'] ) ) {
			$query['posts_per_page'] = -1;
		}

		// Prepare arguments for WP_Query.
		$args = array_merge( $query, [
			'post_status'            => 'any',
			'post_type'              => $post_type,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		] );

		return ( new WP_Query( $args ) )->posts;
	}

	/**
	 * Get module templates by post id.
	 *
	 * @param  int $id
	 *
	 * @return array
	 */
	protected function get_templates( $id ) {
		if ( empty( $id ) && ! is_numeric( $id ) ) {
			return [];
		}

		if ( $data = papi_get_entry_type_by_meta_id( $id ) ) {
			$templates = papi_to_array( $data->template );

			ksort( $templates );

			return $templates;
		}

		return [];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$layout             = $this->get_setting( 'layout' );
		$post_type          = $this->get_post_type();
		$settings           = $this->get_settings();
		$value              = $this->get_value();
		$posts              = $this->get_posts( $post_type );
		$selected_post_id   = is_object( $value ) ? $value->module : 0;
		$selected_post_id   = is_object( $selected_post_id ) ? $selected_post_id->ID : 0;
		$templates          = $this->get_templates( $selected_post_id );
		$selected_template  = is_object( $value ) ? intval( $value->template ) : null;
		$classes            = '';

		if ( $settings->select2 ) {
			$classes .= ' papi-component-select2';
		}
		?>

		<div class="papi-property-module papi-property-post advanced">
			<table class="papi-table">
				<tr>
					<td>
						<label for="<?php echo esc_attr( $this->html_id() ); ?>_modules">
							<?php echo esc_html( $settings->labels['select_module'] ); ?>
						</label>
					</td>
					<td>

						<?php
							$placeholder = ! is_null( $settings->placeholder ) ? $settings->placeholder : '';
						?>

						<select
							class="<?php echo esc_attr( $classes ); ?>  papi-property-module-right"
							id="<?php echo esc_attr( $this->html_id() ); ?>_modules"
							name="<?php echo esc_attr( $this->html_name() ); ?>"
							data-allow-clear="<?php echo empty( $settings->placeholder ) ? 'false' : 'true'; ?>"
							data-select-item="<?php echo esc_attr( $settings->labels['select_module'] ); ?>"
							data-placeholder="<?php echo esc_attr( $settings->placeholder ); ?>"
							data-width="100%"
							>

							<?php if ( ! empty( $settings->placeholder ) ): ?>
								<?php if ( $settings->new_url ): ?>
									<option data-placeholder data-new-url="<?php echo esc_attr( admin_url( 'post-new.php?post_type=' . $post_type ) ); ?>"></option>
								<?php else: ?>
									<option></option>
								<?php endif; ?>
							<?php endif; ?>

							<?php
							foreach ( $posts as $post ) {
								if ( papi_is_empty( $post->post_title ) ) {
									continue;
								}

								papi_render_html_tag( 'option', [
									'data-entry-type' => get_post_meta( $post->ID, papi_get_page_type_key(), true ),
									'data-edit-url'   => get_edit_post_link( $value ),
									'data-new-url'    => $settings->new_url ? admin_url( 'post-new.php?post_type=' . $post->post_type ) : '',
									'selected'        => $selected_post_id === $post->ID,
									'value'           => $post->ID,
									$post->post_title
								] );
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label for="<?php echo esc_attr( $this->html_id() ); ?>_template">
							<?php echo esc_html( $settings->labels['select_template'] ); ?>
						</label>
					</td>
					<td>
						<select
							id="<?php echo esc_attr( $this->html_id() ); ?>_template"
							name="<?php echo esc_attr( $this->html_name() ); ?>_template"
							class="<?php echo esc_attr( $classes ); ?> papi-property-module-left"
							data-post-query='<?php echo esc_attr( papi_maybe_json_encode( $settings->query ) ); ?>'
							data-width="100%"
							>
							<?php
							foreach ( $templates as $index => $item ) {
								// Make string to a array.
								if ( is_string( $item ) ) {
									$item = [
										'label'    => $item,
										'template' => $item,
										'default'  => false
									];
								}

								// Bail if no array.
								if ( ! is_array( $item ) ) {
									continue;
								}

								// Bail if no label or template exists.
								if ( ! isset( $item['label'], $item['template'] ) ) {
									continue;
								}

								// Set missing default value.
								if ( ! isset( $item['default'] ) ) {
									$item['default'] = false;
								}

								papi_render_html_tag( 'option', [
									'value'    => $index,
									'selected' => ! papi_is_empty( $selected_template ) ? $index === $selected_template : $item['default'],
									$item['label']
								] );
							}
							?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	/**
	 * Render option template.
	 */
	public function render_option_template() {
		$settings  = $this->get_settings();
		$post_type = $this->get_post_type();

		?>
		<script type="text/template" id="tmpl-papi-property-module-option">
			<option
				data-allow-clear="<?php echo esc_attr( $settings->allow_clear ); ?>"
				value="<%= value %>"
				>
				<%= title %>
			</option>
		</script>
		<script type="text/template" id="tmpl-papi-property-module-option-placeholder">
			<option
				data-placeholder

				<?php if ( $settings->new_url ): ?>
				data-new-url="<?php echo esc_attr( admin_url( 'post-new.php?post_type=' . $post_type ) ); ?>"
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
