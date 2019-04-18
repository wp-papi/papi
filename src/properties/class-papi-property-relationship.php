<?php

/**
 * Relationship property that can handle more than
 * one relationship between posts or other data items.
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
	 * Convert WordPress post object to a item object.
	 *
	 * @param  WP_Post $post
	 *
	 * @return object
	 */
	protected function convert_post_to_item( WP_Post $post ) {
		return (object) [
			'id'    => $this->get_post_value( $post ),
			'title' => $post->post_title
		];
	}

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $values
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function format_value( $values, $slug, $post_id ) {
		if ( is_array( $values ) || is_object( $values ) ) {
			$items  = $this->get_setting( 'items' );
			$result = [];

			foreach ( $values as $id ) {
				// Backwards compatibility with array `id` and `id`.
				$id = is_object( $id ) ? $id->id : $id;
				$id = is_array( $id ) ? $id['id'] : $id;

				if ( empty( $id ) ) {
					continue;
				}

				if ( papi_is_empty( $items ) ) {
					if ( empty( $this->get_setting( 'meta_key' ) ) ) {
						$post = get_post( $id );
					} else {
						$args = [
							'fields'         => 'ids',
							'meta_key'       => $this->get_setting( 'meta_key' ),
							'meta_value'     => $id,
							'posts_per_page' => 1,
							'post_type'      => $this->get_setting( 'post_type' ),
						];

						$query = new WP_Query( $args );

						if ( ! empty( $query->posts ) ) {
							$post = get_post( $query->posts[0] );
						}
					}

					if ( empty( $post ) ) {
						continue;
					}

					$result[] = $post;
				} else {
					$id   = (int) $id;
					$item = null;

					foreach ( (array) $items as $value ) {
						$ids = wp_list_pluck( [$value], 'id' );
						$ids = count( $ids ) > 0 ? strval( $ids[0] ) : '';

						if ( $ids === (string) $id ) {
							$item = $value;
							break;
						}
					}

					if ( is_array( $item ) || is_object( $item ) ) {
						$result[] = papi_maybe_convert_to_object( $item );
					}
				}
			}

			$values = $this->sort_value( $result, $slug, $post_id );

			// Allow only id to be returned.
			if ( ! papi_is_admin() && $this->get_setting( 'fields' ) === 'ids' ) {
				return array_map( function ( $item ) {
					if ( $id = $this->get_post_value( $item ) ) {
						return $id;
					}

					$id = $item->ID;

					if ( is_numeric( $id ) ) {
						return $id == (int) $id ? (int) $id : (float) $id; // loose comparison
					}

					return $id;
				}, $values );
			}

			return $values;
		}

		return $this->default_value;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'fields'       => '',
			'items'        => [],
			'limit'        => -1,
			'meta_key'     => '',
			'only_once'    => false,
			'post_type'    => 'page',
			'query'        => [],
			'show_sort_by' => true,
			'title'        => __( 'Post', 'papi' )
		];
	}

	/**
	 * Get sort option value.
	 *
	 * @param  int $post_id
	 *
	 * @return string
	 */
	public function get_sort_option( $post_id ) {
		$slug = $this->html_id( 'sort_option' );
		$slug = str_replace( '][', '_', $slug );
		$slug = str_replace( '[', '_', $slug );
		$slug = str_replace( ']', '', $slug );

		return papi_data_get( $post_id, $slug, $this->get_meta_type() );
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
			// Backwards compatibility with both `post_title` and `title`.
			return strcmp(
				strtolower( isset( $a->post_title ) ? $a->post_title : $a->title ),
				strtolower( isset( $b->post_title ) ? $b->post_title : $b->title )
			);
		};

		$sort_options[__( 'Post created date (ascending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_date ) > strtotime( $b->post_date );
		};

		$sort_options[__( 'Post created date (descending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_date ) < strtotime( $b->post_date );
		};

		$sort_options[__( 'Post id (ascending)', 'papi' )] = function ( $a, $b ) {
			// Backwards compatibility with both `ID` and `id`.
			return isset( $a->ID ) ? $a->ID > $b->ID : $a->id > $b->id;
		};

		$sort_options[__( 'Post id (descending)', 'papi' )] = function ( $a, $b ) {
			// Backwards compatibility with both `ID` and `id`.
			return isset( $a->ID ) ? $a->ID < $b->ID : $a->id < $b->id;
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

		return apply_filters( 'papi/property/relationship/sort_options', $sort_options );
	}

	/**
	 * Get items to display from settings.
	 *
	 * @param  stdClass $settings
	 *
	 * @return array
	 */
	protected function get_items( $settings ) {
		if ( is_array( $settings->items ) && ! empty( $settings->items ) ) {
			$mapping = function ( $item ) {
				return is_array( $item ) ?
					isset( $item['id'], $item['title'] ) :
					isset( $item->id, $item->title );
			};

			return array_map(
				'papi_maybe_convert_to_object',
				array_filter( $settings->items, $mapping )
			);
		}

		// By default we add posts per page key with the value -1 (all).
		if ( ! isset( $settings->query['posts_per_page'] ) ) {
			$settings->query['posts_per_page'] = -1;
		}

		// Prepare arguments for WP_Query.
		$args = array_merge( [
			'post_status'            => 'any',
			'post_type'              => papi_to_array( $settings->post_type ),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		], $settings->query );

		$items = ( new WP_Query( $args ) )->posts;

		return array_map( [$this, 'convert_post_to_item'], papi_get_only_objects( $items ) );
	}

	/**
	 * Get matching value based on key from a post.
	 *
	 * @param  mixed $value
	 *
	 * @return mixed
	 */
	protected function get_post_value( $value ) {
		$meta_key = $this->get_setting( 'meta_key' );

		if ( $value instanceof WP_Post === false ) {
			return 0;
		}

		if ( empty( $meta_key ) ) {
			return $value->ID;
		}

		if ( $value = get_post_meta( $value->ID, $meta_key, true ) ) {
			return $value;
		}

		return 0;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$post_id       = papi_get_post_id();
		$slug          = $this->html_name();
		$settings      = $this->get_settings();
		$settings_json = [];
		$sort_option   = $this->get_sort_option( $post_id );
		$sort_options  = static::get_sort_options();
		$values        = papi_get_only_objects( $this->get_value() );
		$items         = $this->get_items( $settings );


		if ( papi_is_empty( $settings->items ) ) {
			$values = array_map( [$this, 'convert_post_to_item'], $values );
		} else {
			foreach ( array_keys( $sort_options ) as $key ) {
				if ( strpos( $key, 'Post' ) === 0 ) {
					unset( $sort_options[$key] );
				}
			}
		}

		// Remove existing values if `only once` is active.
		if ( $this->get_setting( 'only_once' ) ) {
			$items = array_udiff( $items, $values, function( $a, $b ) {
				// Backwards compatibility with both `post_title` and `title`.
				return strcmp(
					strtolower( isset( $a->post_title ) ? $a->post_title : $a->title ),
					strtolower( isset( $b->post_title ) ? $b->post_title : $b->title )
				);
			} );
		}

		// Convert all sneak case key to camel case.
		foreach ( (array) $settings as $key => $val ) {
			if ( ! is_string( $key ) || ! in_array( $key, ['only_once', 'limit'], true ) ) {
				continue;
			}

			if ( $key = papi_camel_case( $key ) ) {
				$settings_json[$key] = $val;
			}
		}
		?>
		<div class="papi-property-relationship" data-settings='<?php echo esc_attr( papi_maybe_json_encode( $settings_json ) ); ?>'>
			<input type="hidden" name="<?php echo esc_attr( $slug ); ?>[]" data-papi-rule="<?php echo esc_attr( $slug ); ?>" />
			<div class="relationship-inner">
				<div class="relationship-top-left">
					<label for="<?php echo esc_attr( $this->html_id( 'search' ) ); ?>"><?php esc_html_e( 'Search', 'papi' ); ?></label>
					<input id="<?php echo esc_attr( $this->html_id( 'search' ) ); ?>" type="search" />
				</div>
				<div class="relationship-top-right">
					<?php if ( $settings->show_sort_by ): ?>
						<label for="<?php echo esc_attr( $this->html_id( 'sort_option' ) ); ?>"><?php esc_html_e( 'Sort by', 'papi' ); ?></label>
						<select id="<?php echo esc_attr( $this->html_id( 'sort_option' ) ); ?>" name="<?php echo esc_attr( $this->html_id( 'sort_option' ) ); ?>">
							<?php foreach ( array_keys( $sort_options ) as $key ): ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php echo $key === $sort_option ? 'selected="selected"' : ''; ?>><?php echo esc_html( $key ); ?></option>
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
						foreach ( $items as $item ):
							if ( ! empty( $item->title ) ):
								?>
								<li>
									<input type="hidden" data-name="<?php echo esc_attr( $slug ); ?>[]" value="<?php echo esc_attr( $item->id ); ?>"/>
									<a href="#" title="<?php echo esc_attr( $item->title ); ?>"><?php echo esc_html( $item->title ); ?></a>
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
						<?php foreach ( $values as $item ): ?>
							<li>
								<input type="hidden" name="<?php echo esc_attr( $slug ); ?>[]" value="<?php echo esc_attr( $item->id ); ?>"/>
								<a href="#"><?php echo esc_attr( $item->title ); ?></a>
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
	 * Change value after it's loaded from the database.
	 *
	 * @param  mixed  $values
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function load_value( $values, $slug, $post_id ) {
		$values = (array) papi_maybe_json_decode( maybe_unserialize( $values ), true );
		return array_map( 'papi_maybe_convert_to_object', $values );
	}

	/**
	 * Sort the values.
	 *
	 * @param  array  $values
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function sort_value( $values, $slug, $post_id ) {
		$sort_option  = $this->get_sort_option( $post_id );
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
	 * @param  mixed  $values
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return string
	 */
	public function update_value( $values, $slug, $post_id ) {
		$values = $this->format_value( $values, $slug, $post_id );
		$values = array_map( function ( $item ) {
			if ( $item instanceof WP_Post ) {
				$item = $this->convert_post_to_item( $item );
			}

			if ( isset( $item->title ) ) {
				unset( $item->title );
			}

			return $item;
		}, $values );

		return papi_maybe_json_decode( $values );
	}
}
