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

		// Database value.
		$value = $this->get_value();

		$settings = $this->get_settings( array(
			'post_type' => 'post'
		) );

		add_thickbox();

		$posts = query_posts( 'post_type=' . $settings->post_type . '&posts_per_page=-1' );
		?>

		<script type="text/template" id="tmpl-papi-post">
			<p>
				<strong><?php _e( 'Selected:', 'papi' ); ?></strong> <%= title %>
			</p>
			<input type="hidden" value="<%= id %>" name="<%= slug %>"/>
			<a href="#"><?php _e( 'Remove', 'papi' ); ?></a>
		</script>

		<div id="<?php echo $options->slug; ?>_box" class="hidden">
			<div class="papi-property-post thickbox" data-slug="<?php echo $options->slug; ?>">
				<h3><?php _e( 'Select post', 'papi' ); ?></h3>
				<p>
					<em><?php _e('Number of posts:', 'papi'); ?> <span><?php echo count($posts); ?></span></em>
				</p>
				<p>
					<input type="search" placeholder="<?php _e('Search', 'papi'); ?>" />
				</p>
				<ul class="papi-post-list">
					<?php foreach ( $posts as $post ): ?>
						<li>
							<a href="#" data-id="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></a>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="submitbox">
					<div id="wp-link-cancel">
						<a class="submitdelete deletion" href="#"><?php _e('Cancel', 'papi'); ?></a>
					</div>
				</div>

				<div class="clear"></div>

			</div>
		</div>

		<div class="papi-property-post" data-slug="<?php echo $options->slug; ?>">
			<p class="papi-post-select <?php echo empty( $options->value ) ? '' : 'hidden'; ?>">
				<?php _e( 'No post selected', 'papi' ); ?>
				<button class="button"><?php _e( 'Select post', 'papi' ); ?></button>
				<a href="#TB_inline?width=600&height=400&inlineId=<?php echo $options->slug; ?>_box" class="hidden thickbox"><?php _e( 'Select post', 'papi' ); ?></a>
			</p>
			<div class="papi-post-value">
				<?php if ( ! empty( $options->value ) ): ?>
					<p>
						<strong><?php _e( 'Selected:', 'papi' ); ?></strong> <?php echo $options->value->post_title; ?>
					</p>

					<input type="hidden" value="<?php echo $options->value->post_id; ?>" name="<?php echo $options->slug; ?>"/>
					<a href="#"><?php _e( 'Remove', 'papi' ); ?></a>
				<?php endif; ?>
			</div>
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
