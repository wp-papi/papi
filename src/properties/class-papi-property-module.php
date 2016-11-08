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
				'select_template' => __( 'Select Template', 'papi' ),
				'select_item'     => __( 'Select Module', 'papi' )
			],
			'layout'        => 'single', // single or advanced
			'placeholder'   => '',
			'post_type'     => 'module',
			'select2'       => true,
			'query'         => []
		];
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

		if ( empty( $post_type ) ) {
			$post_type = $this->get_post_types();
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
	 * Get module templates by post id.
	 *
	 * @param  int $id
	 *
	 * @return array
	 */
	public function get_module_templates( $id ) {
		if ( empty( $id ) && ! is_numeric( $id ) ) {
			return [];
		}

		if ( $data = papi_get_entry_type_by_meta_id( $id ) ) {
			return $data->template;
		}

		return [];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$layout             = $this->get_setting( 'layout' );
		$post_types         = $this->get_post_types();
		$classes            = count( $post_types ) > 1 ? '' : 'papi-fullwidth';
		$render_label       = count( $post_types ) > 1;
		$advanced           = $render_label && $layout === 'advanced';
		$single             = $render_label && $layout !== 'advanced';
		$settings           = $this->get_settings();
		$value              = $this->get_value();
		$value              = is_object( $value ) ? $value->ID : 0;
		$selected_post_type = $value !== 0 && get_post_type( $value ) ? : '';
		$posts              = $this->get_posts( $selected_post_type );
		$templates          = $this->get_module_templates( $posts['Modules'][0]->ID );

		if ( $settings->select2 ) {
			$classes .= ' papi-component-select2';
		}
		?>

		<div class="papi-property-post advanced">
			<table class="papi-table">
				<tr>
					<td>
						<label for="<?php echo esc_attr( $this->html_id() ); ?>_modules">
							<?php echo esc_html( $settings->labels['select_item'] ); ?>
						</label>
					</td>
					<td>
						<select
							class="<?php echo esc_attr( $classes ); ?>  papi-property-module-right"
							id="<?php echo esc_attr( $this->html_id() ); ?>_modules"
							name="<?php echo esc_attr( $this->html_name() ); ?>"
							data-allow-clear="<?php echo empty( $settings->placeholder ) ? 'false' : 'true'; ?>"
							data-placeholder="<?php echo esc_attr( $settings->placeholder ); ?>"
							data-width="100%"
							>

							<?php if ( ! empty( $settings->placeholder ) ): ?>
								<option value=""></option>
							<?php endif; ?>

							<?php foreach ( $posts as $label => $items ) : ?>

								<?php if ( $render_label ): ?>
									<optgroup label="<?php echo esc_attr( $label ); ?>">
								<?php endif; ?>

								<?php
								foreach ( $items as $post ) {
									if ( papi_is_empty( $post->post_title ) ) {
										continue;
									}

									papi_render_html_tag( 'option', [
										'data-edit-url' => get_edit_post_link( $value ),
										'selected'      => $value === $post->ID,
										'value'         => $post->ID,
										$post->post_title
									] );
								}
								?>

								<?php if ( $render_label ): ?>
									</optgroup>
								<?php endif; ?>

							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr style="display: none">
					<td>
						<label for="<?php echo esc_attr( $this->html_id() ); ?>_template">
							<?php echo esc_html( $settings->labels['select_template'] ); ?>
						</label>
					</td>
					<td>
						<select
							id="<?php echo esc_attr( $this->html_id() ); ?>_template"
							class="<?php echo esc_attr( $classes ); ?> papi-property-module-left"
							data-select-item="<?php echo esc_attr( $settings->labels['select_item'] ); ?>"
							data-post-query='<?php echo esc_attr( papi_maybe_json_encode( $settings->query ) ); ?>'
							data-width="100%"
							>
							<?php
							foreach ( $templates as $key => $template ) {
								papi_render_html_tag( 'option', [
									'value'    => $key,
									'selected' => $key === $selected_post_type,
									$key
								] );

								if ( $selected ) {
									$selected_label = $label;
								}
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

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'papi/ajax/get_module_templates', [$this, 'ajax_get_module_templates'] );
	}
}
