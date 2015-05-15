<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

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

	public $default_value = [];

	/**
	 * Get default settings.
	 *
	 * @var array
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'blank_text'    => '',
			'include_blank' => true,
			'post_type'     => 'post',
			'query'         => [],
			'text'          => __( 'Select post', 'papi' )
		];
	}

	/**
	 * Display property html.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$settings   = $this->get_settings();
		$value      = $this->get_value();
		$post_types = papi_to_array( $settings->post_type );

		if ( is_object( $value ) ) {
			$value = $value->ID;
		} else {
			$value = 0;
		}

		// By default we add posts per page key with the value -1 (all).
		if ( ! isset( $settings->query['posts_per_page'] ) ) {
			$settings->query['posts_per_page'] = -1;
		}

		// Prepare arguments for WP_Query.
		$args = array_merge( $settings->query, [
			'post_type'              => $post_types,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		] );

		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		// Keep only objects.
		$posts   = papi_get_only_objects( $posts );
		$results = [];

		// Set labels
		foreach ( $posts as $post ) {
			$obj = get_post_type_object( $post->post_type );
			if ( ! isset( $results[$obj->labels->menu_name] ) ) {
				$results[$obj->labels->menu_name] = [];
			}
			$results[$obj->labels->menu_name][] = $post;
		}

		$posts = $results;
		$render_label = count( $posts ) > 1;

		// The blank item
		if ( $settings->include_blank ) {
			$blank = new stdClass;
			$blank->ID = 0;
			$blank->post_title = papi_esc_html( $settings->blank_text );
			$posts = array_merge( [[$blank]], $posts );
		}

		?>

		<div class="papi-property-post">
			<?php if ( ! empty( $settings->text ) ): ?>
				<p>
					<?php echo $settings->text; ?>
				</p>
			<?php endif; ?>
			<select name="<?php echo $this->html_name(); ?>" class="papi-vendor-select2 papi-fullwidth">

				<?php foreach ( $posts as $label => $items ) : ?>

					<?php if ( $render_label && is_string( $label ) ): ?>
						<optgroup label="<?php echo $label; ?>">
					<?php endif; ?>

					<?php foreach ( $items as $post ): ?>
						<option value="<?php echo $post->ID; ?>" <?php echo $value == $post->ID ? 'selected="selected"' : ''; ?>>
							<?php echo $post->post_title; ?>
						</option>
					<?php endforeach; ?>

					<?php if ( $render_label ): ?>
						</optgroup>
					<?php endif; ?>

				<?php endforeach; ?>

			</select>
		</div>
		<?php
	}

	/**
	 * Format the value of the property before it's returned to the theme.
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
		if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
			return get_post( $value );
		}

		return $this->default_value;
	}

}
