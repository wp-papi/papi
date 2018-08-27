<?php

/**
 * WordPress media file property.
 */
class Papi_Property_File extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'object';

	/**
	 * The default value.
	 *
	 * @var array
	 */
	public $default_value = [];

	/**
	 * File type.
	 *
	 * @var string
	 */
	protected $file_type  = 'file';

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( ! is_array( $value ) ) {
			$value = $this->get_file( $value );
		}

		// Single file.
		if ( is_numeric( $value ) ) {
			$meta = wp_get_attachment_metadata( $value );

			if ( isset( $meta ) && ! empty( $meta ) ) {
				$att  = get_post( $value );
				$mine = [
					'alt'         => trim( strip_tags( get_post_meta( $value, '_wp_attachment_image_alt', true ) ) ),
					'caption'     => trim( strip_tags( $att->post_excerpt ) ),
					'description' => trim( strip_tags( $att->post_content ) ),
					'id'          => intval( $value ),
					'is_image'    => (bool) wp_attachment_is_image( $value ),
					'title'       => esc_html( $att->post_title ),
					'url'         => wp_get_attachment_url( $value ),
				];

				$meta = is_array( $meta ) ? $meta : ['file' => $meta];

				if ( isset( $meta['sizes'] ) ) {
					foreach ( array_keys( $meta['sizes'] ) as $size ) {
						if ( $src = wp_get_attachment_image_src( $mine['id'], $size ) ) {
							$meta['sizes'][$size]['url'] = $src[0];
						}
					}
				}

				$value = (object) array_merge( $meta, $mine );

				if ( ! papi_is_admin() && $this->get_setting( 'fields' ) === 'ids' ) {
					return $this->get_file_value( $value );
				}

				return $value;
			}

			return (int) $value;
		}

		// Multiple files.
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$value[$k] = $this->format_value( $v, $slug, $post_id );
			}

			return $value;
		}

		if ( is_object( $value ) && ! isset( $value->url ) ) {
			return;
		}

		return $value;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'multiple' => false,
			'meta_key' => ''
		];
	}

	/**
	 * Get labels.
	 *
	 * @return array
	 */
	public function get_labels() {
		return [
			'add'     => __( 'Add file', 'papi' ),
			'no_file' => __( 'No file selected', 'papi' )
		];
	}

	/**
	 * Get file by id or meta value.
	 *
	 * @param  mixed $value
	 *
	 * @return int
	 */
	public function get_file( $value ) {
		$meta_key = $this->get_setting( 'meta_key' );

		if ( empty( $meta_key ) ) {
			if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
				$post = get_post( $value );
			}
		} else if ( ! empty( $value ) ) {
			$args = [
				'fields'         => 'ids',
				'meta_key'       => $meta_key,
				'meta_value'     => $value,
				'posts_per_page' => 1,
				'post_type'      => 'attachment',
				'post_status'    => 'any'
			];

			$query = new WP_Query( $args );

			if ( ! empty( $query->posts ) ) {
				$post = get_post( $query->posts[0] );
			}
		}

		if ( empty( $post ) ) {
			return $value;
		}

		return $post->ID;
	}

	/**
	 * Get matching value based on key from a file.
	 *
	 * @param  mixed $value
	 *
	 * @return mixed
	 */
	protected function get_file_value( $value ) {
		$meta_key = $this->get_setting( 'meta_key' );

		if ( ! is_object( $value ) ) {
			return 0;
		}

		if ( empty( $meta_key ) ) {
			return $value->id;
		}

		if ( $value = get_post_meta( $value->id, $meta_key, true ) ) {
			return $value;
		}

		return 0;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$css_classes = '';
		$labels      = $this->get_labels();
		$settings    = $this->get_settings();
		$slug        = $this->html_name();
		$value       = papi_to_array( $this->get_value() );

		// Keep only valid objects.
		$value = array_filter( $value, function ( $item ) {
			return is_object( $item ) && isset( $item->id ) && ! empty( $item->id );
		} );

		$show_button = empty( $value );

		if ( $settings->multiple ) {
			$css_classes .= ' multiple ';
			$slug .= '[]';
			$show_button = true;
		}
		?>

		<div class="papi-property-file <?php echo esc_attr( $css_classes ); ?>" data-file-type="<?php echo esc_attr( $this->file_type ); ?>">
			<p class="papi-file-select <?php echo $show_button ? '' : 'papi-hide'; ?>">
				<?php
				if ( ! $settings->multiple ) {
					echo esc_html( $labels['no_file'] ) . '&nbsp;';
				}

				papi_render_html_tag( 'input', [
					'name'  => esc_attr( $slug ),
					'type'  => 'hidden',
					'value' => ''
				] );

				papi_render_html_tag( 'button', [
					'data-slug' => esc_attr( $slug ),
					'class'     => 'button',
					'type'      => 'button',

					esc_html( $labels['add'] )
				] );
				?>
			</p>
			<div class="attachments">
				<?php
				if ( is_array( $value ) ):
					foreach ( $value as $file ):
						if ( ! is_object( $file ) ) {
							continue;
						}

						$url = wp_get_attachment_thumb_url( $file->id );

						if ( empty( $url ) ) {
							$url = wp_mime_type_icon( $file->id );
						}
						?>
						<div class="attachment">
							<a class="check" href="#">&times;</a>
							<div class="attachment-preview">
								<div class="thumbnail">
									<div class="centered">
										<?php
										papi_render_html_tag( 'img', [
											'alt' => esc_attr( $file->alt ),
											'src' => esc_attr( $url )
										] );

										papi_render_html_tag( 'input', [
											'name'  => esc_attr( $slug ),
											'type'  => 'hidden',
											'value' => $file->id
										] );

										if ( ! isset( $file->file ) && isset( $file->url ) ) {
											$file->file = $file->url;
										}
										?>
									</div>
									<?php if ( $this->file_type === 'file' ): ?>
										<div class="filename">
											<div><?php echo esc_html( basename( $file->file ) ); ?></div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php
					endforeach;
				endif;
				?>
			</div>
			<div class="clear"></div>
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
		if ( $this->get_setting( 'multiple' ) ) {
			$values = [];

			foreach ( papi_to_array( $value ) as $item ) {
				if ( is_object( $item ) && isset( $item->id ) && $this->is_attachment( $item->id ) ) {
					$values[] = $item->id;
				} else if ( is_numeric( $item ) ) {
					if ( $this->is_attachment( $item ) ) {
						$values[] = $item;
					}
				}
			}

			return array_filter( $values, function ( $val ) {
				return ! empty( $val );
			} );
		}

		if ( is_object( $value ) && isset( $value->id ) && $this->is_attachment( $value->id ) ) {
			return (int) $value->id;
		}

		if ( is_numeric( $value ) && $this->is_attachment( (int) $value ) ) {
			return (int) $value;
		}
	}

	/**
	 * Check if the given id is a attachment post type or not.
	 *
	 * @param  int $id
	 *
	 * @return bool
	 */
	protected function is_attachment( $id ) {
		return get_post_type( (int) $id ) === 'attachment';
	}

	/**
	 * Render file template.
	 */
	public function render_file_template() {
		?>
		<script type="text/template" id="tmpl-papi-property-file">
			<a class="check" href="#">&times;</a>
			<div class="attachment-preview">
				<div class="thumbnail">
					<div class="centered">
						<img src="<%= url %>" alt="<%= alt %>"/>
						<input type="hidden" value="<%= id %>" name="<%= slug %>"/>
					</div>
					<% if (typeof filename !== "undefined") { %>
					<div class="filename">
						<div><%= filename %></div>
					</div>
					<% } %>
				</div>
			</div>
		</script>
		<?php
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_head', [$this, 'render_file_template'] );
	}

	/**
	 * Setup filters.
	 */
	protected function setup_filters() {
		add_action( 'wp_get_attachment_metadata', [$this, 'wp_get_attachment_metadata'], 10, 2 );
	}

	/**
	 * Save file with a meta value or id.
	 *
	 * @param  mixed  $values
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return string
	 */
	public function update_value( $values, $slug, $post_id ) {
		if ( ! is_array( $values ) ) {
			$values = $this->get_file_value( (object) [
				'id' => $values
			] );

			if ( empty( $values ) ) {
				return;
			}

			return $values;
		}

		foreach ( $values as $index => $value ) {
			if ( ! is_numeric( $value ) ) {
				continue;
			}

			$values[$index] = $this->get_file_value( (object) [
				'id' => $value
			] );
		}

		return array_filter( $values );
	}

	/**
	 * Get attachment metadata.
	 *
	 * @param  mixed $data
	 * @param  int   $post_id
	 *
	 * @return mixed
	 */
	public function wp_get_attachment_metadata( $data, $post_id ) {
		if ( papi_is_empty( $data ) ) {
			return get_post_meta( $post_id, '_wp_attached_file', true );
		}

		return $data;
	}
}
