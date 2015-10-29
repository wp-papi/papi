<?php

/**
 * Relationship property that can handle
 * more than one relationship between posts.
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
			'id'    => $post->ID,
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
			$items  = $this->get_settings()->items;
			$result = [];

			foreach ( $values as $key => $id ) {
				// Backwards compatibility with array `id` and `id`.
				$id = is_object( $id ) ? $id->id : $id;

				if ( empty( $id ) ) {
					continue;
				}

				if ( papi_is_empty( $items ) ) {
					$post = get_post( $id );

					if ( empty( $post ) ) {
						continue;
					}

					$result[] = $post;
				} else {
					$item = array_filter( $items, function ( $item ) use ( $id ) {
						return wp_list_pluck( [$item], 'id' )[0] === (int) $id;
					} );

					$result[] = papi_maybe_convert_to_object( array_values( $item )[0] );
				}
			}

			return $this->sort_value( $result, $slug, $post_id );
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
			'items'        => [],
			'limit'        => -1,
			'only_once'    => false,
			'post_type'    => 'page',
			'title'        => __( 'Post', 'papi' ),
			'query'        => [],
			'show_sort_by' => true
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
			return strtotime( $a->post_modified ) >
				strtotime( $b->post_modified );
		};

		$sort_options[__( 'Post modified date (descending)', 'papi' )] = function ( $a, $b ) {
			return strtotime( $a->post_modified ) <
				strtotime( $b->post_modified );
		};

		return apply_filters(
			'papi/property/relationship/sort_options',
			$sort_options
		);
	}

	/**
	 * Get items to display from settings.
	 *
	 * @param  object $settings
	 *
	 * @return array
	 */
	protected function get_items( $settings ) {
		if ( is_array( $settings->items ) && ! empty( $settings->items ) ) {
			$mapping = function ( $item ) {
				return is_array( $item ) ?
					isset( $item['id'] ) && isset( $item['title'] ) :
					isset( $item->id ) && isset( $item->title );
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
		$args = array_merge( $settings->query, [
			'post_type'              => papi_to_array( $settings->post_type ),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		] );

		$query = new WP_Query( $args );
		$items = $query->get_posts();

		return array_map(
			[$this, 'convert_post_to_item'],
			papi_get_only_objects( $items )
		);
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
			foreach ( $sort_options as $key => $sort ) {
				if ( strpos( $key, 'Post' ) === 0 ) {
					unset( $sort_options[$key] );
				}
			}
		}

		// Convert all sneak case key to camel case.
		foreach ( (array) $settings as $key => $val ) {
			if ( ! is_string( $key ) || ! in_array( $key, ['only_once', 'limit'] ) ) {
				continue;
			}

			$settings_json[papi_camel_case( $key )] = $val;
		}
		?>
		<div class="papi-property-relationship" data-settings='<?php echo json_encode( $settings_json ); ?>'>
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
							<?php foreach ( $sort_options as $key => $v ): ?>
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
						foreach ( $items as $item ):
							if ( ! empty( $item->title ) ):
								?>
								<li>
									<input type="hidden"
										   data-name="<?php echo $slug; ?>[]"
									       value="<?php echo $item->id; ?>"/>
									<a href="#"><?php echo $item->title; ?></a>
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
								<input type="hidden" name="<?php echo $slug; ?>[]"
								       value="<?php echo $item->id; ?>"/>
								<a href="#"><?php echo $item->title; ?></a>
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
	 * Import value to the property.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( ! is_array( $value ) && ! is_object( $value ) && ! is_numeric( $value ) ) {
			return;
		}

		$values = [];

		foreach ( papi_to_array( $value ) as $index => $val ) {
			if ( $val instanceof WP_Post ) {
				$values[] = $val->ID;
			}

			if ( is_object( $val ) && isset( $val->id ) ) {
				$values[] = (int) $val->id;
			}

			if ( is_numeric( $val ) ) {
				$values[] = (int) $val;
			}
		}

		return $values;
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

		return json_encode( $values );
	}
}
