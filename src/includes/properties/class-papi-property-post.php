<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Post.
 *
 * @package Papi
 */

class Papi_Property_Post extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */

	public $convert_type = 'object';

	/**
	 * Format the value of the property before it's returned to the theme.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
			// Switch site if multisite is activated.
			$this->switch_site();

			$post = get_post( $value );

			// Restore site if multisite is activated.
			$this->restore_site();

			return $post;
		}

		return $this->default_value;
	}

	/**
	 * Get default settings.
	 *
	 * @var array
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'blog_id'       => 1,
			'placeholder'   => '',
			'post_type'     => 'post',
			'query'         => []
		];
	}

	/**
	 * Get posts.
	 *
	 * @param object $settings
	 *
	 * @return array
	 */

	protected function get_posts( $settings ) {
		// By default we add posts per page key with the value -1 (all).
		if ( ! isset( $settings->query['posts_per_page'] ) ) {
			$settings->query['posts_per_page'] = -1;
		}

		// Switch site if multisite is activated.
		$this->switch_site();

		// Prepare arguments for WP_Query.
		$args = array_merge( $settings->query, [
			'post_type'              => papi_to_array( $settings->post_type ),
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

		// Restore site if multisite is activated.
		$this->restore_site();

		return $results;
	}

	/**
	 * Display property html.
	 */

	public function html() {
		$settings   = $this->get_settings();
		$value      = $this->get_value();

		if ( is_object( $value ) ) {
			$value = $value->ID;
		} else {
			$value = 0;
		}

		$posts = $this->get_posts( $settings );
		$render_label = count( $posts ) > 1;
		?>

		<div class="papi-property-post">
			<select
				name="<?php echo $this->html_name(); ?>"
				class="papi-component-select2 papi-fullwidth"
				data-allow-clear="true"
				data-placeholder="<?php echo $settings->placeholder; ?>"
				data-width="100%">

				<?php if ( ! empty( $settings->placeholder ) ): ?>
					<option value=""></option>
				<?php endif; ?>

				<?php foreach ( $posts as $label => $items ) : ?>

					<?php if ( $render_label && is_string( $label ) ): ?>
						<optgroup label="<?php echo $label; ?>">
					<?php endif; ?>

					<?php foreach ( $items as $post ):
						if ( papi_is_empty( $post->post_title ) ) {
							continue;
						}
					?>
						<option value="<?php echo $post->ID; ?>" <?php echo $value === $post->ID ? 'selected="selected"' : ''; ?>>
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

}
