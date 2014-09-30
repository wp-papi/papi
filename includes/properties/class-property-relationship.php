<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Relationship
 *
 * @package Papi
 * @version 1.0.0
 */
class PropertyRelationship extends Papi_Property {

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		// Property options.
		$options = $this->get_options();

		// Property settings.
		$settings = $this->get_settings( array(
			'post_types' => array( 'page' ),
			'show_max'   => - 1
		) );

		// Database value.
		$references = $this->get_value();

		// Fetch posts with the post types.
		$posts = query_posts( array(
			'post_type' => $settings->post_types
		) );

		// Take as many posts we should show.
		$posts = array_slice( $posts, 0, (
		$settings->show_max === - 1 ?
			count( $posts ) :
			$settings->show_max ) );

		if ( ! is_array( $references ) ) {
			$references = array();
		}

		$references = array_filter( $references, function ( $post ) {
			return is_object( $post );
		} );

		?>
		<div class="papi-property-relationship">
			<div class="relationship-inner">
				<div class="relationship-left">
					<div class="relationship-search">
						<input type="search" placeholder="Sök"/>
					</div>
					<ul>
						<?php
						foreach ( $posts as $post ):
							if ( ! empty( $post->post_title ) ):
								?>
								<li>
									<input type="hidden" data-name="<?php echo $options->slug; ?>[]"
									       value="<?php echo $post->ID; ?>"/>
									<a href="#"><?php echo $post->post_title; ?></a>
									<span class="icon plus"></span>
								</li>
							<?php
							endif;
						endforeach;
						?>
					</ul>
				</div>
				<div class="relationship-right">
					<ul>
						<?php foreach ( $references as $post ): ?>
							<li>
								<input type="hidden" name="<?php echo $options->slug; ?>[]"
								       value="<?php echo $post->ID; ?>"/>
								<a href="#"><?php echo $post->post_title; ?></a>
								<span class="icon minus"></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="papi-clear"></div>
			</div>
		</div>
	<?php
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function format_value( $value, $post_id ) {
		if ( is_array( $value ) ) {
			return array_map( function ( $id ) {
				return get_post( $id );
			}, $value );
		} else {
			return array();
		}
	}
}
