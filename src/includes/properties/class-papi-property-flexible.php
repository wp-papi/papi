<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Property Flexible
 *
 * @package Papi
 * @since 1.3.0
 */

class Papi_Property_Flexible extends Papi_Property_Repeater {

	/**
	 * Flexible repeater counter number.
	 *
	 * @var int
	 * @since 1.3.0
	 */

	protected $counter = 0;

	/**
	 * The default value.
	 *
	 * @var array
	 * @since 1.3.0
	 */

	public $default_value = array();

	/**
	 * The layout key.
	 *
	 * @var string
	 * @since 1.3.0
	 */

	private $layout_key = '_layout';

	/**
	 * The layout key.
	 *
	 * @var string
	 * @since 1.3.0
	 */

	private $layout_key_regex = '/\_layout/';

	/**
	 * Layout prefix regex.
	 *
	 * @var string
	 * @since 1.3.0
	 */

	private $layout_prefix_regex = '/^\_flexible\_layout\_/';

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $values
	 * @param string $repeater_slug
	 * @param int $post_id
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function format_value( $values, $repeater_slug, $post_id ) {
		$values = parent::format_value( $values, $repeater_slug, $post_id );

		foreach ( $values as $index => $layout ) {
			foreach ( $layout as $slug => $value ) {
				if ( ! is_string( $value ) || ! preg_match( $this->layout_prefix_regex, $value ) ) {
					continue;
				}

				if ( isset( $values[$index][$this->layout_key] ) ) {
					unset( $values[$index][$slug] );
					continue;
				}

				$values[$index][$this->layout_key] = $value;
				unset( $values[$index][$slug] );
			}
		}

		return $values;
	}

	/**
	 * Get default settings.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'items' => array()
		);
	}

	/**
	 * Generate layout slug.
	 *
	 * @param string $key
	 * @param string $extra
	 * @since 1.3.0
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
	 * @since 1.3.0
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
	 * @since 1.0.0
	 *
	 * @return array
	 */

	protected function get_results( $value, $repeater_slug, $post_id ) {
		global $wpdb;

		$value     = intval( $value );
		$values    = array();
		$table     = $wpdb->prefix . 'postmeta';
		$query     = $wpdb->prepare( "SELECT * FROM `$table` WHERE `meta_key` LIKE '%s' AND `post_id` = %s ORDER BY `meta_id` ASC", $repeater_slug . '_%', $post_id );
		$dbresults = $wpdb->get_results( $query );
		$results   = array();
		$trash     = array();

		// Do not proceed with empty value, columns or dbresults.
		if ( empty( $value ) || empty( $dbresults ) ) {
			return array( array(), array() );
		}

		// Get row results.
		$rows = $this->get_row_results( $dbresults );

		// Get columns, divde all items with two.
		$columns =  array_map( function( $row ) {
			return count( $row ) / 2;
		}, $rows );

		// Add repeater slug with number of rows to the values array.
		$values[$repeater_slug] = $value;

		// Get all properties slugs.
		$slugs = $this->get_settings_properties_slugs();

		for ( $i = 0; $i < $value; $i++ ) {

			$no_trash = array();

			if ( ! isset( $no_trash[$i] ) ) {
				$no_trash[$i] = array();
			}

			if ( ! isset( $columns[$i] ) ) {
				continue;
			}

			for ( $j = 0; $j < $columns[$i]; $j++ ) {

				if ( ! isset( $slugs[$i] ) || ! isset( $slugs[$i][$j] ) ) {
					continue;
				}

				// Generate slug from repeater slug, index and property slug.
				$slug = sprintf( '%s_%d_%s', $repeater_slug, $i, $slugs[$i][$j] );

				if ( ! isset( $rows[$i] ) || ! isset( $rows[$i][$slug] ) ) {
					continue;
				}

				// Get database value.
				$meta = $rows[$i][$slug];

				// Add meta object to the no trash array.
				$no_trash[$i][$slug] = $meta;

				// Get property type key and value.
				$property_type_key   = papi_get_property_type_key( $meta->meta_key );
				$property_type_value = get_post_meta( $post_id, papi_f( $property_type_key ), true );

				// Serialize value if needed.
				$meta->meta_value = maybe_unserialize( $meta->meta_value );

				// Add property value and property type value.
				$values[$meta->meta_key] = maybe_unserialize( $meta->meta_value );
				$values[$property_type_key] = $property_type_value;

				// Add the flexible layout for the property.
				$slug .= '_layout';
				$values[$slug] = $rows[$i][$slug]->meta_value;
			}

			// Get the meta keys to delete.
			$trash_diff = array_diff( array_keys( $rows[$i] ), array_keys( $no_trash[$i] ) );

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

		return array( $values, $trash );
	}

	/**
	 * Get layouts.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_settings_layouts() {
		$settings = $this->get_settings();
		$items    = $settings->items;
		return $this->prepare_properties( papi_to_array( $items ) );
	}

	/**
	 * Get settings properties.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_settings_properties() {
		$layouts = $this->get_settings_layouts();
		$items = array_map( function ( $layout ) {
			return $layout['items'];
		}, $layouts );
		return $this->prepare_properties( papi_to_array( $items ) );
	}

	/**
	 * Get settings properties slugs.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_settings_properties_slugs() {
		$layouts = $this->get_settings_layouts();
		return array_map( function( $layout ) {
			return array_map( function ( $property ) {
				return papi_remove_papi( $property->array_slug );
			}, $layout['items'] );
		}, $layouts );
	}

	/**
	 * Prepare properties.
	 *
	 * Not the best name for this function, but since
	 * property repeater using this we can't rename it.
	 *
	 * @param array $layouts
	 * @since 1.3.0
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
			$layouts[$index]['items'] = parent::prepare_properties( $layout['items'] );
		}

		return $layouts;
	}

	/**
	 * Render layout input.
	 *
	 * @param string $slug
	 * @param string $value
	 * @since 1.3.0
	 */

	protected function render_layout_input( $slug, $value ) {
		$slug = $this->get_property_html_name( array(
			'slug' => $slug . $this->layout_key
		) );
		?>
		<input type="hidden" name="<?php echo $slug; ?>" value="<?php echo $value; ?>" />
		<?php
	}

	/**
	 * Render layout JSON template.
	 *
	 * @param string $slug
	 * @since 1.3.0
	 */

	protected function render_json_template( $slug ) {
		$items = parent::get_settings_properties();
		$index = 0;

		foreach ( $items as $layout ):
			$properties = array();

			foreach ( $layout['items'] as $key => $value ) {
				$properties[$key] = $value;
				$properties[$key]->raw   = true;
				$properties[$key]->slug  = $this->get_property_html_name( $value );
				$properties[$key]->value = '';
			}
			?>

			<script type="application/json" data-papi-json="<?php echo $this->get_json_id( $layout['title'], 'flexible_json' ); ?>">
				<?php echo json_encode( array(
						'layout'     => $this->get_layout_value( 'layout', $layout['slug'] ),
						'properties' => $properties
					) ); ?>
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
	 * @since 1.3.0
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
							$render_property = clone $items[$i];
							$value_slug      = papi_remove_papi( $render_property->slug );

							if ( ! array_key_exists( $value_slug, $value ) ) {
								continue;
							}

							$render_property->value = $value[$value_slug];
							$render_property->slug = $this->get_property_html_name( $render_property );
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
	 * @since 1.3.0
	 */

	protected function render_repeater( $options ) {
		$items = parent::get_settings_properties();
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
							<?php foreach ( $items as $layout ): ?>
								<li data-papi-json="<?php echo $options->slug; ?>_<?php echo $layout['slug']; ?>_flexible_json"><?php echo $layout['title']; ?></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<a href="#" class="button button-primary"><?php _e( 'Add new row', 'papi' ); ?></a>
				</div>
			</div>

			<?php /* Default repeater value */ ?>

			<input type="hidden" name="<?php echo $options->slug; ?>[]" />

			<?php
				/* One underscore is saved, two underscores isn't saved */
				$values = $this->get_value();
			?>

			<input type="hidden" name="__<?php echo $options->slug; ?>_rows" value="<?php echo count( $values ); ?>" class="papi-property-repeater-rows" />

		</div>
		<?php
	}

	/**
	 * Render repeater row.
	 *
	 * @since 1.3.0
	 */

	protected function render_repeater_row() {
		$items  = parent::get_settings_properties();
		$values = $this->get_value();
		$slugs  = $this->get_settings_properties_slugs();

		// Match slugs against database values.
		foreach ( $values as $index => $value ) {
			$keys = array_keys( $value );
			foreach ( $slugs as $layout ) {
				foreach ( $layout as $slug ) {
					if ( in_array( $slug, $keys ) ) {
						continue;
					}
					$values[$index][$slug] = '';
				}
			}
		}

		foreach ( $values as $index => $value ):
			?>

			<tr>
				<td class="handle">
					<span><?php echo $this->counter + 1; ?></span>
				</td>
				<?php
					foreach ( $items as $layout ) {
						// Don't render layouts that don't have a valid value in the database.

						if ( ! isset( $value[$this->layout_key] ) || $this->get_layout_value( 'layout', $layout['slug'] ) !== $value[$this->layout_key] ) {
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
	 *
	 * @since 1.3.0
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
	 * @since 1.3.0
	 *
	 * @return bool
	 */

	private function valid_layout( $layout ) {
		return isset( $layout['title'] ) && isset( $layout['items'] );
	}
}
