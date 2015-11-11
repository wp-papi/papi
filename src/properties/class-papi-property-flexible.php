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
	protected $exclude_properties = ['flexible', 'repeater'];

	/**
	 * The layout key.
	 *
	 * @var string
	 */
	protected $layout_key = '_layout';

	/**
	 * Layout prefix regex.
	 *
	 * @var string
	 */
	private $layout_prefix_regex = '/^\_flexible\_layout\_/';

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
		$rows   = intval( papi_get_property_meta_value( $post_id, $slug ) );
		$value  = $this->load_value( $rows, $slug, $post_id );
		$value  = papi_to_property_array_slugs( $value, $slug );
		$result = true;

		foreach ( $value as $key => $value ) {
			$out    = papi_delete_property_meta_value( $post_id, $key, $type );
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
				if ( is_string( $value ) && preg_match( $this->layout_prefix_regex, $value ) ) {
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
				$property_type = papi_get_property_type( $property_type_value );

				if ( ! is_object( $property_type ) ) {
					continue;
				}

				// Get property child slug.
				$child_slug = $this->get_child_slug( $repeater_slug, $slug );

				// Load the value.
				$values[$index][$slug] = $property_type->load_value(
					$value,
					$child_slug,
					$post_id
				);

				$values[$index][$slug] = papi_filter_load_value(
					$property_type->type,
					$values[$index][$slug],
					$child_slug,
					$post_id
				);

				// Format the value from the property class.
				$values[$index][$slug] = $property_type->format_value(
					$values[$index][$slug],
					$child_slug,
					$post_id
				);

				if ( ! is_admin() ) {
					$values[$index][$slug] = papi_filter_format_value(
						$property_type->type,
						$values[$index][$slug],
						$child_slug,
						$post_id
					);
				}

				$values[$index][$property_type_slug] = $property_type_value;
			}
		}

		if ( ! is_admin() ) {
			foreach ( $values as $index => $row ) {
				foreach ( $row as $slug => $value ) {
					if ( is_string( $value ) && preg_match( $this->layout_prefix_regex, $value ) ) {
						$values[$index][$slug] = preg_replace(
							$this->layout_prefix_regex,
							'',
							$value
						);
					}

					if ( papi_is_property_type_key( $slug ) ) {
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
		return is_string( $key ) && $this->layout_key === $key;
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
		return $this->get_slug() . '_' . papi_slugify( $key ) . (
			empty( $extra ) ? '' : '_' . $extra
		);
	}

	/**
	 * Get layout by slug.
	 *
	 * @param  string $slug
	 *
	 * @return string
	 */
	protected function get_layout( $slug ) {
		$layouts = $this->get_settings_layouts();

		foreach ( $layouts as $layout ) {
			if ( $layout['slug'] === $slug ) {
				return $layout;
			}
		}
	}

	/**
	 * Get layout value.
	 *
	 * @param  string $prefix
	 * @param  string $name
	 *
	 * @return string
	 */
	protected function get_layout_value( $prefix, $name ) {
		return sprintf( '_flexible_%s_%s', $prefix, $name );
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

		$option_page = $this->is_option_page();

		if ( $option_page ) {
			$table = $wpdb->prefix . 'options';
			$query = $wpdb->prepare(
				"SELECT * FROM `$table` WHERE `option_name` LIKE '%s' ORDER BY `option_id` ASC",
				$repeater_slug . '_%'
			);
		} else {
			$table = $wpdb->prefix . 'postmeta';
			$query = $wpdb->prepare(
				"SELECT * FROM `$table` WHERE `meta_key` LIKE '%s' AND `post_id` = %s ORDER BY `meta_id` ASC", $repeater_slug . '_%',
				$post_id
			);
		}

		$dbresults = $wpdb->get_results( $query );
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

		for ( $i = 0; $i < $value; $i++ ) {

			$no_trash = [];

			if ( ! isset( $columns[$i] ) || ! isset( $rows[$i] ) ) {
				continue;
			}

			foreach ( $rows[$i] as $slug => $meta ) {
				if ( ! is_string( $slug ) || ! isset( $rows[$i][$slug] ) ) {
					continue;
				}

				// Do not deal with layout meta object here since the property meta object will deal with it later.
				if ( is_string( $meta->meta_value ) && preg_match( $this->layout_prefix_regex, $meta->meta_value ) ) {
					if ( ! isset( $values[$slug] ) ) {
						$values[$slug] = $meta->meta_value;
					}

					continue;
				}

				// Add meta object to the no trash array.
				// so it won't be deleted.
				$no_trash[$slug] = $meta;

				// Get property type key and value.
				$property_type_key   = papi_get_property_type_key_f(
					$meta->meta_key
				);
				$property_type_value = papi_get_property_meta_value(
					$post_id,
					$property_type_key
				);

				// Serialize value if needed.
				$meta->meta_value = papi_maybe_json_decode(
					maybe_unserialize( $meta->meta_value )
				);

				// Add property value and property type value.
				$values[$meta->meta_key] = $meta->meta_value;
				$values[$property_type_key] = $property_type_value;

				// Add the flexible layout for the property.
				if ( ! preg_match( '/\_layout$/', $slug ) && is_string( $rows[$i][$slug]->meta_value ) && ! preg_match( $this->layout_prefix_regex, $rows[$i][$slug]->meta_value ) ) {
					$slug .= '_layout';
				}

				if ( isset( $rows[$i][$slug] ) ) {
					// Add the meta value.
					$values[$slug] = $rows[$i][$slug]->meta_value;
				}
			}

			// Get the meta keys to delete.
			$trash_diff = array_diff(
				array_keys( $rows[$i] ),
				array_keys( $no_trash )
			);

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

		$dblayouts = [];

		// Fetch one layout per row.
		foreach ( array_keys( $values ) as $slug ) {
			if ( preg_match( '/\\' . $this->layout_key . '$/', $slug ) ) {
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
		for ( $i = 0; $i < $value; $i++ ) {
			foreach ( $layouts as $layout ) {
				if ( isset( $layout['slug'] ) && in_array( $i . $layout['slug'], $dblayouts ) ) {
					foreach ( $layout['items'] as $prop ) {
						$slug = sprintf( '%s_%d_%s', $repeater_slug, $i, papi_remove_papi( $prop->slug ) );

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

		$page    = $this->get_page();
		$results = papi_from_property_array_slugs(
			$results,
			papi_remove_papi( $repeater_slug )
		);

		if ( is_null( $page ) ) {
			return $this->default_value;
		}

		foreach ( $results as $index => $row ) {
			foreach ( $row as $slug => $value ) {
				if ( papi_is_property_type_key( $slug ) ) {
					continue;
				}

				if ( $property = $page->get_property( $repeater_slug, $slug ) ) {
					$type_key = papi_get_property_type_key_f( $slug );
					$results[$index][$type_key] = $property;
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

			$layouts[$index]['slug']  = papi_slugify( $layout['slug'] );
			$layouts[$index]['slug']  = $this->get_layout_value(
				'layout',
				$layouts[$index]['slug']
			);
			$layouts[$index]['items'] = parent::prepare_properties(
				$layout['items']
			);
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
			$counter = papi_get_qs( 'counter' );
			$this->counter  = intval( $counter );
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
				// Don't show the property if it's disabled.
				if ( $property->disabled() ) {
					unset( $options->settings->items[$key]['items'][$index] );
					continue;
				}

				$options->settings->items[$key]['items'][$index] = clone $property->get_options();
			}
		}
		?>
		<script type="application/json" data-papi-json="<?php echo $slug; ?>_repeater_json">
			<?php echo json_encode( [$options] ); ?>
		</script>
		<?php
	}

	/**
	 * Render layout input.
	 *
	 * @param string $slug
	 * @param string $value
	 */
	protected function render_layout_input( $slug, $value ) {
		// Creating a fake hidden property to generate right slug.
		$slug = $this->html_name( papi_property( [
			'type' => 'hidden',
			'slug' => $slug . $this->layout_key
		] ), $this->counter );
		?>
		<input type="hidden" name="<?php echo $slug; ?>" value="<?php echo $value; ?>" />
		<?php
	}

	/**
	 * Render properties.
	 *
	 * @param array $row
	 * @param array|bool $value
	 */
	protected function render_properties( $row, $value ) {
		$has_value     = $value !== false;
		$layout_slug   = isset( $row['slug'] ) ? $row['slug'] : $value['_layout'];
		$render_layout = $this->get_setting( 'layout' );
		$row           = isset( $row['items'] ) ? $row['items'] : $row;
		?>
			<td class="repeater-column flexible-column <?php echo $render_layout === 'table' ? 'flexible-layout-table' : 'flexible-layout-row'; ?>">
				<div class="repeater-content-open">
					<table class="<?php echo $render_layout === 'table' ? 'flexible-table' : 'papi-table'; ?>">
						<?php
						if ( $render_layout === 'table' ):
							echo '<thead>';
							for ( $i = 0, $l = count( $row ); $i < $l; $i++ ) {
								// Don't show the property if it's disabled.
								if ( $row[$i]->disabled() ) {
									continue;
								}

								echo '<th class="' . ( $row[$i]->display() ? '' : 'papi-hide' ) . '">';
								echo sprintf(
									'<label for="%s">%s</label>',
									$this->html_id( $row[$i], $this->counter ),
									$row[$i]->title
								);
								echo '</th>';
							}
							echo '</thead>';
						endif;

						echo '<tbody>';

						if ( $render_layout === 'table' ):
							echo '<tr>';
						endif;

						for ( $i = 0, $l = count( $row ); $i < $l; $i++ ) {
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

							$render_property->slug  = $this->html_name(
								$render_property,
								$this->counter
							);
							$render_property->raw   = $render_layout === 'table';

							if ( $render_layout === 'table' ) {
								echo '<td class="' . ( $row[$i]->display() ? '' : 'papi-hide' ) . '">';
							}

							$layout_value = isset( $layout_slug ) ?
								$layout_slug : $value[$this->layout_key];

							$this->render_layout_input(
								$value_slug,
								$layout_value
							);

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
					if ( $layout = $this->get_layout( $layout_slug ) ) {
						echo $layout['title'];
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
		<div class="papi-property-flexible papi-property-repeater-top" data-limit="<?php echo $this->get_setting( 'limit' ); ?>">
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
										'data-layout'    => $layout['slug'],
										'data-papi-json' => sprintf( '%s_repeater_json', $options->slug ),
										'href'           => '#',
										'role'           => 'button',
										'tabindex'       => 0,
										$layout['title']
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

			<input type="hidden" data-papi-rule="<?php echo $options->slug; ?>" name="<?php echo $this->get_slug(); ?>[]" />
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
				$slugs[] = papi_remove_papi( $item->slug );
			}
		}

		// Remove values that don't exists in the slugs array.
		foreach ( $values as $index => $row ) {
			$keys = array_keys( $row );

			foreach ( $row as $slug => $value ) {
				if ( in_array( $slug, $keys ) || papi_is_property_type_key( $slug ) || $this->is_layout_key( $slug ) ) {
					continue;
				}

				unset( $values[$index][$slug] );
			}
		}

		$values = array_filter( $values );
		$closed_rows = $this->get_setting( 'closed_rows', true );

		foreach ( $values as $index => $row ):
			?>

			<tr <?php echo $closed_rows ? 'class="closed"' : ''; ?>>
				<td class="handle">
					<span class="toggle"></span>
					<span class="count"><?php echo $this->counter + 1; ?></span>
				</td>
				<?php
				foreach ( $layouts as $layout ) {
					// Don't render layouts that don't have a valid value in the database.
					if ( ! isset( $row[$this->layout_key] ) || $layout['slug'] !== $row[$this->layout_key] ) {
						continue;
					}

					// Render all properties in the layout
					$this->render_properties( $layout['items'], $row );
				}

				$this->counter++;
				?>
				<td class="last">
					<span>
						<a title="<?php _e( 'Remove', 'papi' ); ?>" href="#" class="repeater-remove-item">x</a>
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
						<a title="<?php _e( 'Remove', 'papi' ); ?>" href="#" class="repeater-remove-item">x</a>
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
	 * @param array $layout
	 *
	 * @return bool
	 */
	private function valid_layout( $layout ) {
		return isset( $layout['title'] ) && isset( $layout['items'] );
	}
}
