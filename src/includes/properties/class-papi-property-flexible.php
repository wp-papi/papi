<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Property Flexible
 *
 * @package Papi
 */

class Papi_Property_Flexible extends Papi_Property_Repeater {

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
	 * The layout key.
	 *
	 * @var string
	 */

	private $layout_key = '_layout';

	/**
	 * Layout prefix regex.
	 *
	 * @var string
	 */

	private $layout_prefix_regex = '/^\_flexible\_layout\_/';

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $values
	 * @param string $repeater_slug
	 * @param int $post_id
	 *
	 * @return array
	 */

	public function format_value( $values, $repeater_slug, $post_id ) {

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

				// Get property type
				$property_type_value = $values[$index][$property_type_slug];
				$property_type = papi_get_property_type( $property_type_value );

				if ( ! is_object( $property_type ) ) {
					continue;
				}

				// Run update value on each property type class.
				$values[$index][$slug] = $property_type->format_value( $value, $slug, $post_id );

				$values[$index][$property_type_slug] = $property_type_value;
			}
		}

		if ( ! is_admin() ) {
			foreach ( $values as $index => $row ) {
				foreach ( $row as $slug => $value ) {
					if ( is_string( $value ) && preg_match( $this->layout_prefix_regex, $value ) ) {
						$values[$index][$slug] = preg_replace( $this->layout_prefix_regex, '', $value );
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
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'items' => []
		];
	}

	/**
	 * Generate layout slug.
	 *
	 * @param string $key
	 * @param string $extra
	 *
	 * @return string
	 */

	protected function get_json_id( $key, $extra = '' ) {
		$options = $this->get_options();
		return $options->slug . '_' . papi_slugify( $key ) . ( empty( $extra ) ? '' : '_' . $extra );
	}

	/**
	 * Get layout value.
	 *
	 * @param string $prefix
	 * @param string $name
	 *
	 * @return string
	 */

	protected function get_layout_value( $prefix, $name ) {
		return sprintf( '_flexible_%s_%s', $prefix, $name );
	}

	/**
	 * Get results from the database.
	 *
	 * @param int $value
	 * @param int $post_id
	 * @param string $repeater_slug
	 * @param integer $post_id
	 *
	 * @return array
	 */

	protected function get_results( $value, $repeater_slug, $post_id ) {
		global $wpdb;

		$option_page = $this->is_option_page();

		if ( $option_page ) {
			$table = $wpdb->prefix . 'options';
			$query = $wpdb->prepare( "SELECT * FROM `$table` WHERE `option_name` LIKE '%s' ORDER BY `option_id` ASC", $repeater_slug . '_%' );
		} else {
			$table = $wpdb->prefix . 'postmeta';
			$query = $wpdb->prepare( "SELECT * FROM `$table` WHERE `meta_key` LIKE '%s' AND `post_id` = %s ORDER BY `meta_id` ASC", $repeater_slug . '_%', $post_id );
		}

		$dbresults = $wpdb->get_results( $query );
		$value     = intval( $value );

		// Do not proceed with empty value, columns or dbresults.
		if ( empty( $value ) || empty( $dbresults ) ) {
			return [[], []];
		}

		$values  = [];
		$results = [];
		$trash   = [];

		// Get row results.
		$rows = $this->get_row_results( $dbresults );

		// Get columns, divde all items with two.
		$columns = array_map( function( $row ) {
			return count( $row ) / 2;
		}, $rows );

		// Add repeater slug with number of rows to the values array.
		$values[papi_remove_papi( $repeater_slug )] = $value;

		for ( $i = 0; $i < $value; $i++ ) {

			$no_trash = [];

			if ( ! isset( $columns[$i] ) || ! isset( $rows[$i] ) ) {
				continue;
			}

			foreach ( $rows[$i] as $slug => $meta ) {
				// Do not deal with layout meta object here since the property meta object will deal with it later.
				if ( is_string( $meta->meta_value ) && preg_match( $this->layout_prefix_regex, $meta->meta_value ) ) {
					continue;
				}

				// Add meta object to the no trash array.
				// so it won't be deleted.
				$no_trash[$slug] = $meta;

				// Get property type key and value.
				$property_type_key = papi_get_property_type_key_f( $meta->meta_key );

				if ( $option_page ) {
					$property_type_value = get_option( papi_f( papify( $property_type_key ) ) );
				} else {
					$property_type_value = get_post_meta( $post_id, $property_type_key, true );
				}

				// Serialize value if needed.
				$meta->meta_value = maybe_unserialize( $meta->meta_value );

				// Add property value and property type value.
				$values[papi_remove_papi( $meta->meta_key )] = maybe_unserialize( $meta->meta_value );
				$values[$property_type_key] = $property_type_value;

				// Add the flexible layout for the property.
				if ( ! preg_match( '/\_layout$/', $slug ) && ! preg_match( $this->layout_prefix_regex, $rows[$i][$slug]->meta_value ) ) {
					$slug .= '_layout';
				}

				if ( isset( $rows[$i][$slug] ) ) {
					// Add the meta value.
					$values[papi_remove_papi( $slug )] = $rows[$i][$slug]->meta_value;
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
	 * Prepare properties.
	 *
	 * Not the best name for this function, but since
	 * property repeater using this we can't rename it.
	 *
	 * @param array $layouts
	 *
	 * @return array
	 */

	protected function prepare_properties( $layouts ) {
		foreach ( $layouts as $index => $layout ) {
			if ( ! $this->valid_layout( $layout ) ) {
				unset( $layout[$index] );
				continue;
			}

			if ( ! isset( $layout['slug'] ) ) {
				$layout['slug'] = $layout['title'];
			}

			$layouts[$index]['slug']  = papi_slugify( $layout['slug'] );
			$layouts[$index]['slug']  = $this->get_layout_value( 'layout', $layouts[$index]['slug'] );
			$layouts[$index]['items'] = parent::prepare_properties( $layout['items'] );
		}

		return array_filter( $layouts );
	}

	/**
	 * Render layout input.
	 *
	 * @param string $slug
	 * @param string $value
	 */

	protected function render_layout_input( $slug, $value ) {
		$slug = $this->html_name( [
			'slug' => $slug . $this->layout_key
		], $this->counter );
		?>
		<input type="hidden" name="<?php echo $slug; ?>" value="<?php echo $value; ?>" />
		<?php
	}

	/**
	 * Render layout JSON template.
	 *
	 * @param string $slug
	 */

	protected function render_json_template( $slug ) {
		$layouts = $this->get_settings_layouts();
		$index   = 0;

		foreach ( $layouts as $layout ):
			$properties = [];

			foreach ( $layout['items'] as $key => $value ) {
				$properties[$key] = $value;
				$properties[$key]->raw   = true;
				$properties[$key]->slug  = $this->html_name( $value, $this->counter );
				$properties[$key]->value = '';
			}
			?>

			<script type="application/json" data-papi-json="<?php echo $this->get_json_id( $layout['title'], 'flexible_json' ); ?>">
				<?php echo json_encode( [
						'layout'     => $layout['slug'],
						'properties' => $properties
					] ); ?>
			</script>

			<?php
			$index++;
		endforeach;
	}

	/**
	 * Render properties.
	 *
	 * @param array $items
	 * @param array $value
	 */

	protected function render_properties( $items, $value ) {
		?>
			<td class="flexible-td">
				<table class="flexible-table">
					<thead>
						<?php
						for ( $i = 0, $l = count( $items ); $i < $l; $i++ ) {
							if ( $i === $l - 1 ) {
								echo '<td class="flexible-td-last">';
							} else {
								echo '<td>';
							}

							echo $items[$i]->title;
							echo '</td>';
						}
						?>
					</thead>
					<tbody>
						<tr>
						<?php
						for ( $i = 0, $l = count( $items ); $i < $l; $i++ ) {
							$render_property = $items[$i];
							$value_slug      = papi_remove_papi( $render_property->slug );

							if ( array_key_exists( $value_slug, $value ) ) {
								$render_property->value = $value[$value_slug];
							}

							$render_property->slug = $this->html_name( $render_property, $this->counter );
							$render_property->raw  = true;

							if ( $i === $l - 1 ) {
								echo '<td class="flexible-td-last">';
							} else {
								echo '<td>';
							}

							$this->render_layout_input( $value_slug, $value[$this->layout_key] );
							papi_render_property( $render_property );

							echo '</td>';
						}
						?>
						</tr>
					</tbody>
				</table>
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

		<div class="papi-property-flexible papi-property-repeater-top">
			<table class="papi-table">
				<tbody class="repeater-tbody">
					<?php $this->render_repeater_row(); ?>
				</tbody>
			</table>

			<div class="bottom">
				<div class="flexible-layouts-btn-wrap">
					<div class="flexible-layouts papi-hide">
						<div class="flexible-layouts-arrow"></div>
						<ul>
							<?php foreach ( $layouts as $layout ): ?>
								<li data-papi-json="<?php echo $this->get_json_id( $layout['title'], 'flexible_json' ); ?>">
									<?php echo $layout['title']; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<a href="#" class="button button-primary"><?php _e( 'Add new row', 'papi' ); ?></a>
				</div>
			</div>

			<?php /* Default repeater value */ ?>

			<input type="hidden" name="<?php echo $options->slug; ?>[]" />

			<?php $values = $this->get_value(); ?>

			<input type="hidden" name="__<?php echo $options->slug; ?>_rows" value="<?php echo count( $values ); ?>" class="papi-property-repeater-rows" />

		</div>
		<?php
	}

	/**
	 * Render repeater row.
	 */

	protected function render_repeater_row() {
		$layouts = $this->get_settings_layouts();
		$values  = $this->get_value();

		foreach ( $values as $index => $value ):
			?>

			<tr>
				<td class="handle">
					<span><?php echo $this->counter + 1; ?></span>
				</td>
				<?php
				foreach ( $layouts as $layout ) {
					// Don't render layouts that don't have a valid value in the database.
					if ( ! isset( $value[$this->layout_key] ) || $layout['slug'] !== $value[$this->layout_key] ) {
						continue;
					}

					// Render all properties in the layout
					$this->render_properties( $layout['items'], $value );
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
					<span><%= counter + 1 %></span>
				</td>
				<td class="flexible-td">
					<table class="flexible-table">
						<thead>
							<tr>
								<%= heads %>
							</tr>
						</thead>
						<tbody>
							<tr>
								<%= columns %>
							</tr>
						</tbody>
					</table>
				</td>
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
