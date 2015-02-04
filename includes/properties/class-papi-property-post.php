<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Post.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Post extends Papi_Property {

	/**
	 * The default value.
	 *
	 * @var null
	 * @since 1.0.0
	 */

	public $default_value = null;

	/**
	 * Get default settings.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public function get_default_settings() {
		return array(
			'include_blank' => true,
			'post_type'     => 'post',
			'query'         => array(),
			'text'          => __( 'Select post', 'papi' )
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options  = $this->get_options();
		$settings = $this->get_settings();
		$value    = $this->get_value();

		if ( is_object( $value ) ) {
			$value = $value->ID;
		} else {
			$value = 0;
		}

		// By default we add posts per page key with the value -1 (all).
		if ( ! isset( $settings->query['posts_per_page'] ) ) {
			$settings->query['posts_per_page'] = -1;
		}

		// Fetch posts with the post types and the query.
		$posts = query_posts( array_merge( $settings->query, array(
			'post_type' => _papi_to_array( $settings->post_type )
		) ) );

		// The blank item
		if ( $settings->include_blank ) {
			$blank = new stdClass;
			$blank->ID = 0;
			$blank->post_title = '';

			$posts = array_merge( array( $blank ), $posts );
		}

		// Keep only objects.
		$value = array_filter( _papi_to_array( $value ), function ( $post ) {
			return is_object( $post ) && isset( $post->post_title );
		} );

		?>

		<div class="papi-property-post">
			<?php if ( !empty($settings->text) ): ?>
				<p>
					<?php echo $settings->text; ?>
				</p>
			<?php endif; ?>
			<select name="<?php echo $options->slug; ?>" class="papi-vendor-select2 papi-fullwidth">

				<?php foreach ( $posts as $post ) : ?>

					<option value="<?php echo $post->ID; ?>" <?php echo $value == $post->ID ? 'selected="selected"' : ''; ?>>
						<?php echo $post->post_title; ?>
					</option>

				<?php endforeach; ?>

			</select>
		</div>
		<?php
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) ) {
			return get_post( $value );
		}

		return $this->default_value;
	}

}
