<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Relationship.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Relationship extends Papi_Property {

	/**
	 * The default value.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	public $default_value = array();

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'choose_max'   => -1,
			'post_type'    => 'page',
			'query'        => array(),
			'show_sort_by' => true
		);
	}

	/**
	 * Get sort option value.
	 *
	 * @param string $slug
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */

	public function get_sort_option( $slug ) {
		$post_id = _papi_get_post_id();

		if ( empty( $post_id ) ) {
			return null;
		}

		$slug = _papi_f( _papify( $slug ) . '_sort_option' );

		return get_post_meta( $post_id, $slug, true );
	}

	/**
	 * Get sort options for relationship property.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public static function get_sort_options() {
		$sort_options = array();

		$sort_options[__( 'Select', 'papi' )] = function () {
			return 0;
		};

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

		$sort_options[__( 'Post modified date (ascending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_modified ) > strtotime( $b->post_modified );
		};

		$sort_options[__( 'Post modified date (descending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_modified ) < strtotime( $b->post_modified );
		};

		$sort_options = apply_filters( 'papi_property_relationship_sort_options', $sort_options );
		return $sort_options;
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options     = $this->get_options();
		$settings    = $this->get_settings();
		$sort_option = $this->get_sort_option( $options->slug );
		$value       = $this->get_value();

		// By default we add posts per page key with the value -1 (all).
		if ( ! isset( $settings->query['posts_per_page'] ) ) {
			$settings->query['posts_per_page'] = -1;
		}

		// Fetch posts with the post types and the query.
		$posts = query_posts( array_merge( $settings->query, array(
			'post_type' => _papi_to_array( $settings->post_type )
		) ) );

		// Keep only objects.
		$value = array_filter( _papi_to_array( $value ), function ( $post ) {
			return is_object( $post ) && isset( $post->post_title );
		} );

		?>
		<div class="papi-property-relationship">
			<div class="relationship-inner">
				<div class="relationship-top-left">
					<strong><?php _e( 'Search', 'papi' ); ?></strong>
					<input type="search" />
				</div>
				<div class="relationship-top-right">
					<?php if ( $settings->show_sort_by ): ?>
						<strong><?php _e( 'Sort by', 'papi' ); ?></strong>
						<select name="_<?php echo $options->slug; ?>_sort_option">
							<?php foreach ( static::get_sort_options() as $key => $v ): ?>
								<option value="<?php echo $key; ?>" <?php echo $key == $sort_option ? 'selected="selected"' : ''; ?>><?php echo $key; ?></option>
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
				<div class="relationship-right" data-relationship-choose-max="<?php echo $settings->choose_max; ?>">
					<ul>
						<?php foreach ( $value as $post ): ?>
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
	 * Sort the values.
	 *
	 * @param array $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function sort_value( $value, $slug, $post_id ) {
		$sort_option  = $this->get_sort_option( $slug );
		$sort_options = static::get_sort_options();

		if ( empty( $sort_option ) || ! isset( $sort_options[$sort_option] ) ) {
			return $value;
		}

		usort( $value, $sort_options[$sort_option] );

		return $value;
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
		if ( is_array( $value ) ) {
			$value = array_map( function ( $id ) {
				$post = get_post( $id );

				if ( empty( $post ) ) {
					return $id;
				}

				return $post;
			}, $value );
			return $this->sort_value( $value, $slug, $post_id );
		} else {
			return $this->default_value;
		}
	}

	/**
	 * Sort the values on update.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function update_value( $value, $slug, $post_id ) {
		$value = $this->format_value( $value, $slug, $post_id );

		return array_map( function ( $post ) {
			return $post->ID;
		}, $value );
	}
}
