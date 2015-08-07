<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Relationship class.
 *
 * @package Papi
 */
class Papi_Property_Relationship extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'array';

	/**
	 * The default value.
	 *
	 * @var array
	 */
	public $default_value = [];

	/**
	 * Format the value of the property before it's returned to the application.
	 *
	 * @param mixed $values
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function format_value( $values, $slug, $post_id ) {
		if ( is_array( $values ) ) {
			$values = array_map( function ( $id ) {
				$post = get_post( $id );

				if ( empty( $post ) ) {
					return $id;
				}

				return $post;
			}, array_filter( $values ) );
			return $this->sort_value( $values, $slug, $post_id );
		} else {
			return $this->default_value;
		}
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'limit'        => -1,
			'post_type'    => 'page',
			'query'        => [],
			'show_sort_by' => true
		];
	}

	/**
	 * Get sort option value.
	 *
	 * @param int $post_id
	 * @param string $slug
	 *
	 * @return null|string
	 */
	public function get_sort_option( $post_id, $slug ) {
		$slug = papi_f( papify( $slug ) . '_sort_option' );
		return papi_get_property_meta_value( $post_id, $slug );
	}

	/**
	 * Get sort options for relationship property.
	 *
	 * @return array
	 */
	public static function get_sort_options() {
		$sort_options = [];

		$sort_options[__( 'Select', 'papi' )] = null;

		$sort_options[__( 'Name (alphabetically)', 'papi' )] = function ( $a, $b ) {
			return strcmp( strtolower( $a->post_title ), strtolower( $b->post_title ) );
		};

		$sort_options[__( 'Post created date (ascending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_date ) > strtotime( $b->post_date );
		};

		$sort_options[__( 'Post created date (descending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_date ) < strtotime( $b->post_date );
		};

		$sort_options[__( 'Post id (ascending)', 'papi' )] = function ( $a, $b ) {
			return $a->ID > $b->ID;
		};

		$sort_options[__( 'Post id (descending)', 'papi' )] = function ( $a, $b ) {
			return $a->ID < $b->ID;
		};

		$sort_options[__( 'Post order value (ascending)', 'papi' )] = function ( $a, $b ) {
			return $a->menu_order > $b->menu_order;
		};

		$sort_options[__( 'Post order value (descending)', 'papi' )] = function ( $a, $b ) {
			return $a->menu_order < $b->menu_order;
		};

		$sort_options[__( 'Post modified date (ascending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_modified ) > strtotime( $b->post_modified );
		};

		$sort_options[__( 'Post modified date (descending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_modified ) < strtotime( $b->post_modified );
		};

		$sort_options = apply_filters( 'papi/property/relationship/sort_options', $sort_options );
		return $sort_options;
	}

	/**
	 * Display property html.
	 */
	public function html() {
		$post_id     = papi_get_post_id();
		$slug        = $this->html_name();
		$settings    = $this->get_settings();
		$sort_option = $this->get_sort_option( $post_id, $slug );
		$values       = $this->get_value();

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
		$posts = papi_get_only_objects( $posts );

		?>
		<div class="papi-property-relationship">
			<input type="hidden" name="<?php echo $slug; ?>[]" data-papi-rule="<?php echo $slug; ?>" />
			<div class="relationship-inner">
				<div class="relationship-top-left">
					<label for="<?php echo $this->html_id( 'search' ); ?>"><?php _e( 'Search', 'papi' ); ?></label>
					<input id="<?php echo $this->html_id( 'search' ); ?>" type="search" />
				</div>
				<div class="relationship-top-right">
					<?php if ( $settings->show_sort_by ): ?>
						<label for="<?php echo $this->html_id( 'sort_option' ); ?>"><?php _e( 'Sort by', 'papi' ); ?></label>
						<select id="<?php echo $this->html_id( 'sort_option' ); ?>" name="<?php echo $this->html_id( 'sort_option' ); ?>">
							<?php foreach ( static::get_sort_options() as $key => $v ): ?>
								<option value="<?php echo $key; ?>" <?php echo $key === $sort_option ? 'selected="selected"' : ''; ?>><?php echo $key; ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</div>
				<div class="papi-clear"></div>
			</div>
			<div class="relationship-inner">
				<div class="relationship-left">
					<ul>
						<?php
						foreach ( $posts as $post ):
							if ( ! empty( $post->post_title ) ):
								?>
								<li>
									<input type="hidden"
										   data-name="<?php echo $slug; ?>[]"
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
				<div class="relationship-right" data-limit="<?php echo $settings->limit; ?>">
					<ul>
						<?php foreach ( $values as $post ): ?>
							<li>
								<input type="hidden" name="<?php echo $slug; ?>[]"
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
	 * Sort the values.
	 *
	 * @param array $values
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function sort_value( $values, $slug, $post_id ) {
		$sort_option  = $this->get_sort_option( $post_id, $slug );
		$sort_options = static::get_sort_options();

		if ( empty( $sort_option ) || ! isset( $sort_options[$sort_option] ) || is_null( $sort_options[$sort_option] ) ) {
			return $values;
		}

		usort( $values, $sort_options[$sort_option] );

		return $values;
	}

	/**
	 * Sort the values on update.
	 *
	 * @param mixed $values
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function update_value( $values, $slug, $post_id ) {
		$values = $this->format_value( $values, $slug, $post_id );
		return wp_list_pluck( $values, 'ID' );
	}
}
