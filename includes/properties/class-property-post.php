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
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		// Property options.
		$options = $this->get_options();

		// Property settings
		$settings = $this->get_settings( array(
			'post_type' => 'post',
			'intro'     => __('Select post', 'papi')
		) );

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
				<?php echo $settings->intro; ?>
			</p>
			<select name="<?php echo $options->slug; ?>" class="papi-vendor-select2 papi-fullwidth">

				<?php foreach ($posts as $post) : ?>

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
