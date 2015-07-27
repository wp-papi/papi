<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Post class.
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
	 * Get default settings.
	 *
	 * @var array
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'placeholder'   => '',
			'post_type'     => 'post',
			'select2'       => true,
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

		$posts        = $this->get_posts( $settings );
		$render_label = count( $posts ) > 1;
		$classes      = 'papi-fullwidth';

		if ( $settings->select2 ) {
			$classes = ' papi-component-select2';
		}
		?>

		<div class="papi-property-post">
			<select
				id="<?php echo $this->html_id(); ?>"
				name="<?php echo $this->html_name(); ?>"
				class="<?php echo $classes; ?>"
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

	/**
	 * Format the value of the property before it's returned to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
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
