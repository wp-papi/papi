<?php

/**
 * Property that shows which relationships that exists
 * between the current post and other posts.
 */
class Papi_Property_Reference extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'slug'      => [],
			'page_type' => []
		];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$post_id  = papi_get_post_id();
		$settings = $this->get_settings();

		// Create query array for every page type.
		$page_types = array_map( function ( $page_type ) {
			return [
				'key'     => papi_get_page_type_key(),
				'value'   => $page_type,
				'compare' => 'LIKE'
			];
		}, papi_to_array( $settings->page_type ) );

		// Add relation.
		$page_types['relation'] = 'OR';

		// Prepare arguments for WP_Query.
		$args = [
			'post_type'              => 'any',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => $page_types
		];

		$posts = ( new WP_Query( $args ) )->posts;

		$values = [];

		foreach ( papi_to_array( $settings->slug ) as $slug ) {
			foreach ( $posts as $post ) {
				$val = papi_get_field( $post->ID, $slug );

				$val = array_filter( papi_to_array( $val ), function ( $item ) use ( $post_id ) {
					return is_object( $item ) && $item->ID === $post_id;
				} );

				if ( empty( $val ) ) {
					continue;
				}

				$page_type = papi_get_entry_type_by_meta_id( $post->ID );

				if ( empty( $page_type ) ) {
					continue;
				}

				// Create the array
				if ( ! isset( $values[$post->post_type] ) ) {
					$values[$post->post_type] = [];
				}

				if ( ! isset( $values[$post->post_type][$page_type->name] ) ) {
					$values[$post->post_type][$page_type->name] = [];
				}

				// Add the post
				if ( ! isset( $values[$post->post_type][$page_type->name][$post->ID] ) && ! empty( $val ) ) {
					$values[$post->post_type][$page_type->name][$post->ID] = $post;
				}
			}
		}

		?>
		<ul class="papi-property-reference" data-papi-rule="<?php echo esc_attr( $this->html_name() ); ?>">
			<?php if ( empty( $values ) ): ?>
				<p>
					<?php esc_html_e( 'No references exists', 'papi' ); ?>
				</p>
			<?php
			endif;
			ksort( $values ); foreach ( $values as $title => $val ):
				$post_type = get_post_type_object( $title );
				?>
				<li>
					<h3><?php echo esc_html( $post_type->labels->name ); ?></h3>
					<div class="handlediv" title="Click to toggle"><br></div>
				</li>
				<li>
					<div class="page-types">
						<ul>
							<?php ksort( $val ); foreach ( $val as $name => $posts ): ?>
								<li class="heading-border">
									<h4><?php echo esc_html( $name ); ?></h4>
									<div class="handlediv" title="Click to toggle"><br></div>
								</li>
								<li>
									<div class="box">
										<?php $i = 0;
										foreach ( $posts as $post ): ?>
											<a href="<?php echo esc_attr( get_edit_post_link( $post->ID ) ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
										<?php $i++;
										endforeach; ?>
										<div class="clear"></div>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
						<div class="clear"></div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
