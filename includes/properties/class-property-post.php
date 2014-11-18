<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Post
 *
 * @package Papi
 * @version 1.0.0
 */
class PropertyPost extends Papi_Property {

	/**
	 * The default value.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	public $default_value = 0;

	/**
	 * Get default settings.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public function get_default_settings() {
		return array(
			'text'      => __( 'Select post', 'papi' ),
			'post_type' => 'post'
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		// Property options.
		$options = $this->get_options();

		// Property settings
		$settings = $this->get_settings();

		$value = $this->get_value();

		if ( is_object( $value ) ) {
			$value = $value->ID;
		} else {
			$value = 0;
		}

		$posts = query_posts( 'post_type=' . $settings->post_type . '&posts_per_page=-1' );

		?>

		<div class="papi-property-post">
			<p>
				<?php echo $settings->text; ?>
			</p>
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

	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) ) {
			return get_post( $value );
		}

		return null;
	}

}
