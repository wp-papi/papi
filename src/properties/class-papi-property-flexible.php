<?php

/**
 * Flexible repeater property that can repeat multiple properties
 * with different properties per layout.
 */
class Papi_Property_Flexible extends Papi_Property_Repeater {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'array';

	/**
	 * Flexible repeater counter number.
	 *
	 * @var int
	 */
	protected $counter = 0;

	/**
	 * The default value.
	 *
	 * @var array
	 */
	public $default_value = [];

	/**
	 * Exclude properties that is not allowed in a repeater.
	 *
	 * @var array
	 */
	protected $exclude_properties = ['flexible'];

	/**
	 * The layout key.
	 *
	 * @var string
	 */
	protected $layout_key = '_flexible_layout';

	/**
	 * Layout value regex.
	 *
	 * @var string
	 */
	protected $layout_value_regex = '/^\_flexible\_layout\_/';

	/**
	 * Delete value from the database.
	 *
	 * @param  string $slug
	 * @param  int    $post_id
	 * @param  string $type
	 *
	 * @return bool
	 */
	public function delete_value( $slug, $post_id, $type ) {
		$rows   = intval( papi_data_get( $post_id, $slug, $type ) );
		$value  = $this->load_value( $rows, $slug, $post_id );
		$value  = papi_property_to_array_slugs( $value, $slug );
		$result = true;

		foreach ( array_keys( $value ) as $key ) {
			$out    = papi_data_delete( $post_id, $key, $type );
			$result = $out ? $result : $out;
		}

		return $result;
	}

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $values
	 * @param  string $repeater_slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function format_value( $values, $repeater_slug, $post_id ) {
		if ( ! is_array( $values ) ) {
			return [];
		}

		foreach ( $values as $index => $layout ) {
			foreach ( $layout as $slug => $value ) {
				if ( is_string( $value ) && preg_match( $this->layout_value_regex, $value ) ) {
					if ( isset( $values[$index][$this->layout_key] ) ) {
						unset( $values[$index][$slug] );
						continue;
					}

					$values[$index][$this->layout_key] = $value;
					unset( $values[$index][$slug] );

					continue;
				}

				if ( papi_is_property_type_key( $slug ) ) {
					continue;
				}

				$property_type_slug = papi_get_property_type_key_f( $slug );

				if ( ! isset( $values[$index][$property_type_slug] ) ) {
					continue;
				}

				$property_type_value = $values[$index][$property_type_slug];
				$property_type       = papi_get_property_type( $property_type_value );

				if ( ! is_object( $property_type ) ) {
					continue;
				}

				// Get property child slug.
				$child_slug = $this->get_child_slug( $repeater_slug, $slug );

				// Create cache key.
				$cache_key = sprintf( '%s_%d_%s', $repeater_slug, $index, $slug );

				// Get raw value from cache if enabled.
				if ( $this->cache ) {
					$raw_value = papi_cache_get( $cache_key, $post_id, $this->get_meta_type() );
				} else {
					$raw_value = false;
				}

				// Load the value.
				if ( $raw_value === null || $raw_value === false ) {
					$values[$index][$slug] = $property_type->load_value( $value, $child_slug, $post_id );
					$values[$index][$slug] = papi_filter_load_value( $property_type->type, $values[$index][$slug], $child_slug, $post_id, papi_get_meta_type() );

					if ( ! papi_is_empty( $values[$index][$slug] ) && $this->cache ) {
						papi_cache_set( $cache_key, $post_id, $values[$index][$slug], $this->get_meta_type() );
					}
				} else {
					$values[$index][$slug] = $raw_value;
				}

				if ( strtolower( $property_type->type ) === 'repeater' ) {
					$property_type->cache = false;
				}

				// Format the value from the property class.
				$values[$index][$slug] = $property_type->format_value( $values[$index][$slug], $child_slug, $post_id );

				if ( ! papi_is_admin() ) {
					$values[$index][$slug] = papi_filter_format_value( $property_type->type, $values[$index][$slug], $child_slug, $post_id, papi_get_meta_type() );
				}

				$values[$index][$property_type_slug] = $property_type_value;
			}
		}

		if ( ! papi_is_admin() ) {
			foreach ( $values as $index => $row ) {
				foreach ( $row as $slug => $value ) {
					if ( is_string( $value ) && preg_match( $this->layout_value_regex, $value ) ) {
						unset( $values[$index][$slug] );
						$values[$index]['_layout'] = preg_replace( $this->layout_value_regex, '', $value );
					}

					if ( papi_is_property_type_key( $slug ) ) {
						unset( $values[$index][$slug] );
					}

					if ( papi_is_empty( $value ) ) {
						unset( $values[$index][$slug] );
					}
				}
			}
		}

		return $values;
	}

	/**
	 * Check if the given key is a valid layout key.
	 *
	 * @param  string $key
	 *
	 * @return bool
	 */
	protected function is_layout_key( $key ) {
		return is_string( $key ) && preg_match( '/\\_layout|\\' . $this->layout_key . '$/', $key );
	}

	/**
	 * Generate layout slug.
	 *
	 * @param  string $key
	 * @param  string $extra
	 *
	 * @return string
	 */
	protected function get_json_id( $key, $extra = '' ) {
		return $this->get_slug() . '_' . papi_slugify( $key ) . ( empty( $extra ) ? '' : '_' . $extra );
	}

	/**
	 * Get layout by slug.
	 *
	 * @param  string $slug
	 *
	 * @return array
	 */
	protected function get_layout( $slug ) {
		$layouts = $this->get_settings_layouts();

		foreach ( $layouts as $layout ) {
			if ( $layout['slug'] === $slug ) {
				return $layout;
			}
		}

		return [];
	}

	/**
	 * Get layout value.
	 *
	 * @param  string $layout
	 *
	 * @return string
	 */
	protected function get_layout_value( $layout ) {
		if ( preg_match( $this->layout_value_regex, $layout ) ) {
			return $layout;
		}

		return sprintf( '_flexible_layout_%s', $layout );
	}

	/**
	 * Get results from the database.
	 *
	 * @param  int    $value
	 * @param  string $repeater_slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	protected function get_results( $value, $repeater_slug, $post_id ) {
		global $wpdb;

		if ( $this->get_meta_type() === 'option' ) {
			$table = $wpdb->prefix . 'options';
			// @codingStandardsIgnoreStart
			$query = $wpdb->prepare(
				"SELECT * FROM `$table` WHERE `option_name` LIKE '%s' ORDER BY `option_id` ASC",
				$repeater_slug . '_%'
			);
			// @codingStandardsIgnoreEnd
		} else {
			$table  = sprintf( '%s%smeta', $wpdb->prefix, $this->get_meta_type() );
			$column = papi_get_meta_id_column( $this->get_meta_type() );
			// @codingStandardsIgnoreStart
			$query = $wpdb->prepare(
				"SELECT * FROM `$table` WHERE `meta_key` LIKE '%s' AND `$column` = %s ORDER BY `meta_id` ASC", $repeater_slug . '_%',
				$post_id
			);
			// @codingStandardsIgnoreEnd
		}

		$dbresults = $wpdb->get_results( $query ); // WPCS: unprepared sql
		$value     = intval( $value );

		// Do not proceed with empty value or columns.
		if ( empty( $value ) ) {
			return [[], []];
		}

		$values  = [];
		$results = [];
		$trash   = [];

		// Get row results.
		$rows = $this->get_row_results( $dbresults );

		// Get columns, divde all items with two.
		$columns = array_map( function ( $row ) {
			return count( $row ) / 2;
		}, $rows );

		$rows = array_values( $rows );

		// Add repeater slug with number of rows to the values array.
		$values[$repeater_slug] = $value;

		for ( $i = 0; $i < $value; $i ++ ) {
			$no_trash = [];

			if ( ! isset( $columns[$i] ) || ! isset( $rows[$i] ) ) {
				continue;
			}

			foreach ( $rows[$i] as $slug => $meta ) {
				if ( ! is_string( $slug ) || ! isset( $rows[$i][$slug] ) ) {
					continue;
				}

				// Do not deal with layout meta object here since the property meta object will deal with it later.
				if ( is_string( $meta->meta_value ) && preg_match( $this->layout_value_regex, $meta->meta_value ) ) {
					if ( ! isset( $values[$slug] ) ) {
						$values[$slug] = $meta->meta_value;
					}

					continue;
				}

				// Add meta object to the no trash array.
				// so it won't be deleted.
				$no_trash[$slug] = $meta;

				// Serialize value if needed.
				$meta->meta_value = papi_maybe_json_decode( maybe_unserialize( $meta->meta_value ) );

				// Add property value and property type value.
				$values[$meta->meta_key] = $meta->meta_value;

				if ( isset( $rows[$i][$slug] ) ) {
					// Add the meta value.
					$values[$slug] = $rows[$i][$slug]->meta_value;
				}
			}

			// Get the meta keys to delete.
			$trash_diff = array_diff( array_keys( $rows[$i] ), array_keys( $no_trash ) );

			if ( ! empty( $trash_diff ) ) {
				// Find all trash meta objects from results array.
				foreach ( $trash_diff as $slug ) {
					if ( ! isset( $results[$i] ) || ! isset( $rows[$i][$slug] ) ) {
						continue;
					}

					$trash[$results[$i][$slug]->meta_key] = $rows[$i][$slug];
				}
			}
		}

		// Fetch one layout per row.
		// Since 3.0.0 this is backward compatibility.
		$dblayouts = [];
		foreach ( array_keys( $values ) as $slug ) {
			if ( $this->is_layout_key( $slug ) ) {
				$num = str_replace( $repeater_slug . '_', '', $slug );
				$num = explode( '_', $num );
				$num = intval( $num[0] );

				if ( ! isset( $dblayouts[$num] ) ) {
					$dblayouts[$num] = $num . $values[$slug];
				}
			}
		}

		$layouts = $this->get_settings_layouts();

		// Add empty rows that isn't saved to database.
		for ( $i = 0; $i < $value; $i ++ ) {
			foreach ( $layouts as $layout ) {
				$layout_slug = sprintf( '%s_%d%s', $this->get_slug( true ), $i, $this->layout_key );

				// Since 3.0.0 the `$dblayouts` check is only for backward compatibility.
				if ( isset( $layout['slug'] ) && ( ( isset( $values[$layout_slug] ) && $layout['slug'] === $values[$layout_slug] ) || in_array( $i . $layout['slug'], $dblayouts, true ) ) ) {
					foreach ( $layout['items'] as $prop ) {
						$slug = sprintf( '%s_%d_%s', $repeater_slug, $i, unpapify( $prop->slug ) );

						if ( ! isset( $values[$slug] ) ) {
							$values[$slug] = null;
						}
					}
				}
			}
		}

		return [$values, $trash];
	}

	/**
	 * Get layouts.
	 *
	 * @return array
	 */
	protected function get_settings_layouts() {
		$settings = $this->get_settings();

		return $this->prepare_properties( papi_to_array( $settings->items ) );
	}

	/**
	 * Change value after it's loaded from the database
	 * and populate every property in the flexible with the right property type.
	 *
	 * @param mixed  $value
	 * @param string $repeater_slug
	 * @param int    $post_id
	 *
	 * @return array
	 */
	public function load_value( $value, $repeater_slug, $post_id ) {
		if ( is_array( $value ) ) {
			return $value;
		}

		list( $results, $trash ) = $this->get_results( $value, $repeater_slug, $post_id );

		// Will not need this array.
		unset( $trash );

		$store   = $this->get_store();
		$results = papi_property_from_array_slugs( $results, unpapify( $repeater_slug ) );

		if ( is_null( $store ) ) {
			return $this->default_value;
		}

		return $this->load_child_properties( $results, $this );
	}

	/**
	 * Load child properties.
	 *
	 * @param  array              $results
	 * @param  Papi_Core_Property $property
	 *
	 * @return array
	 */
	protected function load_child_properties( array $results, $property = null ) {
		$layout_key = substr( $this->layout_key, 1 );

		foreach ( $results as $index => $row ) {
			foreach ( $row as $slug => $value ) {
				$children = [];

				if ( $layout_key === $slug ) {
					continue;
				}

				if ( isset( $results[$index][$layout_key] ) ) {
					$layout = $results[$index][$layout_key];
					$layout = $this->get_layout( $layout );

					if ( ! empty( $layout ) && isset( $layout['items'] ) ) {
						$children = $layout['items'];
					}
				}

				$child_property = null;

				foreach ( $children as $child ) {
					if ( $child->match_slug( $slug ) ) {
						$child_property = $child;
					}
				}

				if ( empty( $child_property ) ) {
					$child_property = $this->get_store()->get_property( $this->get_slug( true ), $slug );
				}

				if ( is_array( $value ) && papi_is_property( $child_property ) && ! empty( $child_property->get_child_properties() ) ) {
					$new_value = papi_from_property_array_slugs( $value, unpapify( $slug ) );

					if ( empty( $new_value ) ) {
						$results[$index][$slug] = $value;
					} else {
						$results[$index][$slug] = $this->load_child_properties( $new_value, $child_property );
					}
				}

				$type_key = papi_get_property_type_key_f( $slug );

				if ( $property->match_slug( $slug ) ) {
					$results[$index][$type_key] = $property;
				} else {
					$results[$index][$type_key] = $property->get_child_property( $slug, $children );
				}
			}
		}

		return $results;
	}

	/**
	 * Prepare properties.
	 *
	 * Not the best name for this function, but since
	 * property repeater using this we can't rename it.
	 *
	 * @param  array $layouts
	 *
	 * @return array
	 */
	protected function prepare_properties( $layouts ) {
		$layouts = array_map( function ( $layout ) {
			return (array) $layout;
		}, $layouts );

		foreach ( $layouts as $index => $layout ) {
			if ( ! $this->valid_layout( $layout ) ) {
				if ( is_array( $layout ) ) {
					unset( $layout[$index] );
				} else {
					unset( $layouts[$index] );
				}

				continue;
			}

			if ( ! isset( $layout['slug'] ) ) {
				$layout['slug'] = $layout['title'];
			}

			if ( ! isset( $layout['row_label'] ) ) {
				$layout['row_label'] = $layout['title'];
			}

			if ( ! isset( $layout['show_label'] ) ) {
				$layout['show_label'] = true;
			}

			$layouts[$index] = array_merge( $layouts[$index], $layout );

			$layouts[$index]['slug']  = papi_slugify( $layout['slug'] );
			$layouts[$index]['slug']  = $this->get_layout_value( $layouts[$index]['slug'] );
			$layouts[$index]['items'] = parent::prepare_properties( $layout['items'] );
		}

		return array_filter( $layouts );
	}

	/**
	 * Render AJAX request.
	 */
	public function render_ajax_request() {
		$items   = null;
		$layouts = $this->get_settings_layouts();

		if ( defined( 'DOING_PAPI_AJAX' ) && DOING_PAPI_AJAX ) {
			$counter         = papi_get_qs( 'counter' );
			$this->counter   = intval( $counter );
			$flexible_layout = papi_get_qs( 'flexible_layout' );

			foreach ( $layouts as $layout ) {
				if ( $layout['slug'] === $flexible_layout ) {
					$items = $layout;
					break;
				}
			}
		}

		if ( ! empty( $items ) ) {
			$this->render_properties( $items, false );
		}
	}

	/**
	 * Render layout JSON template.
	 *
	 * @param string $slug
	 */
	protected function render_json_template( $slug ) {
		$options = $this->get_options();

		foreach ( $options->settings->items as $key => $value ) {
			if ( ! isset( $value['items'] ) ) {
				continue;
			}

			foreach ( $value['items'] as $index => $property ) {
				$property = $this->prepare_property_for_json( papi_property( $property ) );

				if ( $property === false ) {
					unset( $options->settings->items[$key]['items'][$index] );
					continue;
				}

				$options->settings->items[$key]['items'][$index] = $property;
			}
		}

		papi_render_html_tag( 'script', [
			'data-papi-json' => esc_attr( sprintf( '%s_repeater_json', $slug ) ),
			'type'           => 'application/json',
			papi_maybe_json_encode( [$options] )
		] );
	}

	/**
	 * Render layout input.
	 *
	 * @param string $value
	 */
	protected function render_layout_input( $value ) {
		$slug = sprintf( '%s[%d][%s]', $this->get_slug(), $this->counter, $this->layout_key );
		?>
		<input type="hidden" name="<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}

	/**
	 * Render properties.
	 *
	 * @param array      $row
	 * @param array|bool $value
	 */
	protected function render_properties( $row, $value ) {
		$has_value     = $value !== false;
		$render_layout = $this->get_setting( 'layout' );
		$layout_slug   = isset( $row['slug'] ) ? $row['slug'] : false;
		$layout_slug   = empty( $layout_slug ) && isset( $value['_layout'] ) ? $value['_layout'] : $layout_slug;
		$layout_slug   = empty( $layout_slug ) && isset( $value[$this->layout_key] ) ? $value[$this->layout_key] : $layout_slug;
		$row           = isset( $row['items'] ) ? $row['items'] : $row;
		$layout        = $this->get_layout( $layout_slug );

		// Render one hidden input for layout slug.
		$this->render_layout_input( $layout_slug );
		?>
		<td class="repeater-column flexible-column <?php echo $render_layout === 'table' ? 'flexible-layout-table' : 'flexible-layout-row'; ?>">
			<div class="repeater-content-open">
				<?php // flexible label table layout ?>
				<?php if ( $render_layout === 'table' && ! empty( $layout['row_label'] ) && isset( $layout['show_label'] ) && $layout['show_label'] ): ?>
					<label class="flexible-row-label"><?php echo esc_html( $layout['row_label'] ); ?></label>
				<?php endif; ?>
				<table class="<?php echo $render_layout === 'table' ? 'flexible-table' : 'papi-table'; ?>">
					<?php
					if ( $render_layout === 'table' ):
						echo '<thead>';
						for ( $i = 0, $l = count( $row ); $i < $l; $i ++ ) {
							// Don't show the property if it's disabled.
							if ( $row[$i]->disabled() ) {
								continue;
							}

							if ( $row[$i]->sidebar ) {
								echo '<th class="' . ( $row[$i]->display() ? '' : 'papi-hide' ) . '">';
								echo sprintf(
									'<label for="%s">%s</label>',
									esc_attr( $this->html_id( $row[$i], $this->counter ) ),
									esc_html( $row[$i]->title )
								);
								echo '</th>';
							}
						}
						echo '</thead>';
					endif;

					echo '<tbody>';

					if ( $render_layout === 'table' ):
						echo '<tr>';
					endif;

					for ( $i = 0, $l = count( $row ); $i < $l; $i ++ ) {
						// Don't show the property if it's disabled.
						if ( $row[$i]->disabled() ) {
							continue;
						}

						$render_property = clone $row[$i]->get_options();
						$value_slug      = $row[$i]->get_slug( true );

						if ( $has_value ) {
							if ( array_key_exists( $value_slug, $value ) ) {
								$render_property->value = $value[$value_slug];
							} else {
								if ( array_key_exists( $row[$i]->get_slug(), $value ) ) {
									$render_property->value = $row[$i]->default_value;
								} else {
									continue;
								}
							}
						}

						$render_property->slug = $this->html_name(
							$render_property,
							$this->counter
						);
						$render_property->raw  = $render_layout === 'table';

						if ( $render_layout === 'table' ) {
							echo '<td class="' . ( $row[$i]->display() ? '' : 'papi-hide' ) . '">';
						} else if ( $i === 0 && ! empty( $layout['row_label'] ) && isset( $layout['show_label'] ) && $layout['show_label'] ) {
							// flexible label row layout
							echo sprintf( '<label class="flexible-row-label">%s</label>', esc_html( $layout['row_label'] ) );
						}

						papi_render_property( $render_property );

						if ( $render_layout === 'table' ) {
							echo '</td>';
						}
					}

					if ( $render_layout === 'table' ):
						echo '</tr>';
					endif;

					echo '</tbody>';
					?>
				</table>
			</div>
			<div class="repeater-content-closed">
				<?php
				if ( ! empty( $layout['title'] ) ) {
					echo esc_html( $layout['title'] );
				}
				?>
			</div>
		</td>
		<?php
	}

	/**
	 * Render repeater html.
	 *
	 * @param object $options
	 */
	protected function render_repeater( $options ) {
		$layouts = $this->get_settings_layouts();
		?>
		<div class="papi-property-flexible papi-property-repeater-top" data-limit="<?php echo esc_attr( $this->get_setting( 'limit' ) ); ?>">
			<table class="papi-table">
				<tbody class="repeater-tbody flexible-tbody">
				<?php $this->render_repeater_row(); ?>
				</tbody>
			</table>

			<div class="bottom">
				<div class="flexible-layouts-btn-wrap">
					<div class="flexible-layouts papi-hide">
						<div class="flexible-layouts-arrow"></div>
						<ul>
							<?php
							foreach ( $layouts as $layout ) {
								papi_render_html_tag( 'li', [
									papi_html_tag( 'a', [
										'data-layout'    => esc_html( $layout['slug'] ),
										'data-papi-json' => sprintf( '%s_repeater_json', $options->slug ),
										'href'           => '#',
										'role'           => 'button',
										'tabindex'       => 0,
										esc_html( $layout['title'] )
									] )
								] );
							}
							?>
						</ul>
					</div>

					<?php
					papi_render_html_tag( 'button', [
						'class' => 'button button-primary',
						'type'  => 'button',
						esc_html( $this->get_setting( 'add_new_label' ) )
					] );
					?>
				</div>
			</div>

			<?php /* Default repeater value */ ?>

			<input type="hidden" data-papi-rule="<?php echo esc_attr( $options->slug ); ?>" name="<?php echo esc_attr( $this->get_slug() ); ?>[]" />
		</div>
		<?php
	}

	/**
	 * Render repeater row.
	 */
	protected function render_repeater_row() {
		$layouts = $this->get_settings_layouts();
		$values  = $this->get_value();

		// Fetch all slugs in all layouts.
		$slugs = [];
		foreach ( $layouts as $index => $layout ) {
			foreach ( $layout['items'] as $item ) {
				$slugs[] = unpapify( $item->slug );
			}
		}

		// Remove values that don't exists in the slugs array.
		foreach ( $values as $index => $row ) {
			$keys = array_keys( $row );

			foreach ( array_keys( $row ) as $slug ) {
				if ( in_array( $slug, $keys, true ) || papi_is_property_type_key( $slug ) || $this->is_layout_key( $slug ) ) {
					continue;
				}

				unset( $values[$index][$slug] );
			}
		}

		$values      = array_filter( $values );
		$closed_rows = $this->get_setting( 'closed_rows', true );

		foreach ( $values as $index => $row ):
			?>

			<tr <?php echo $closed_rows ? 'class="closed"' : ''; ?>>
				<td class="handle">
					<span class="toggle"></span>
					<span class="count"><?php echo esc_html( $this->counter + 1 ); ?></span>
				</td>
				<?php
				foreach ( $layouts as $layout ) {
					// Don't render layouts that don't have a valid value in the database.
					if ( ! isset( $row[$this->layout_key] ) || $layout['slug'] !== $this->get_layout_value( $row[$this->layout_key] ) ) {
						continue;
					}

					// Render all properties in the layout
					$this->render_properties( $layout['items'], $row );
				}

				$this->counter ++;
				?>
				<td class="last">
					<span>
						<a title="<?php esc_html_e( 'Remove', 'papi' ); ?>" href="#" class="repeater-remove-item">x</a>
					</span>
				</td>
			</tr>

			<?php
		endforeach;
	}

	/**
	 * Render repeater row template.
	 */
	public function render_repeater_row_template() {
		?>
		<script type="text/template" id="tmpl-papi-property-flexible-row">
			<tr>
				<td class="handle">
					<span class="toggle"></span>
					<span class="count"><%= counter + 1 %></span>
				</td>
				<%= columns %>
				<td class="last">
					<span>
						<a title="<?php esc_html_e( 'Remove', 'papi' ); ?>" href="#" class="repeater-remove-item">x</a>
					</span>
				</td>
			</tr>
		</script>
		<?php
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_head', [$this, 'render_repeater_row_template'] );
	}

	/**
	 * Check if the layout is valid or not.
	 *
	 * @param  array $layout
	 *
	 * @return bool
	 */
	protected function valid_layout( array $layout ) {
		return isset( $layout['title'], $layout['items'] );
	}
}
