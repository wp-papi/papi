<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Reference.
 *
 * @package Papi
 * @since 1.2.0
 */

class Papi_Property_Reference extends \Papi_Property {

	/**
	 * The default value.
	 *
	 * @var null
	 * @since 1.2.0
	 */

	public $default_value = null;

	/**
	 * Get default settings.
	 *
	 * @var array
	 * @since 1.2.0
	 */

	public function get_default_settings() {
		return array(
			'slug'      => array(),
			'post_type' => 'post',
			'page_type' => array()
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.2.0
	 */

	public function html() {
		$settings = $this->get_settings();

		// Fetch posts with the post types and the query.
		$posts = query_posts( array(
			'post_type' => papi_to_array( $settings->post_type ),
			'meta_query' => array(
				array(
					'key' => PAPI_PAGE_TYPE_KEY,
					'value' => papi_to_array( $settings->page_type ),
					'compare' => 'LIKE'
				)
			)
		) );

		$values = array();

		foreach ( papi_to_array( $settings->slug ) as $slug ) {
			foreach ( $posts as $post ) {
				$val = papi_field( $post->ID, $slug, null, true );

				if ( empty( $val ) ) {
					continue;
				}

				$page_type = papi_get_file_data( $post->ID );

				if ( empty( $page_type ) ) {
					continue;
				}

				// Create the array
				if ( !isset( $values[$post->post_type] ) ) {
					$values[$post->post_type] = array();
				}

				if ( !isset( $values[$post->post_type][$page_type->name] ) ) {
					$values[$post->post_type][$page_type->name] = array();
				}

				// Add the post
				if ( !isset( $values[$post->post_type][$page_type->name][$post->ID] ) && ! empty( $val ) ) {
					$values[$post->post_type][$page_type->name][$post->ID] = $post;
				}
			}
		}

		?>
		<ul class="papi-property-reference">
			<?php ksort( $values ); foreach ( $values as $title => $val ): ?>
				<?php $post_type = get_post_type_object( $title ); ?>
				<li>
					<h3><?php echo $post_type->labels->name; ?></h3>
					<div class="handlediv" title="Click to toggle"><br></div>
				</li>
				<li>
					<div class="page-types">
						<ul>
							<?php ksort( $val ); foreach ( $val as $name => $posts ): ?>
								<li class="heading-border">
									<h4><?php echo $name; ?></h4>
									<div class="handlediv" title="Click to toggle"><br></div>
								</li>
								<li>
									<div class="box">
										<?php $i = 0; foreach ( $posts as $post ): ?>
											<a href="<?php echo get_edit_post_link( $post->ID ); ?>"><?php echo $post->post_title; ?></a>
										<?php $i++; endforeach; ?>
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
