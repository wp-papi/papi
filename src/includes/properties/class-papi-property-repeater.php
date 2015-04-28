<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Repeater.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Repeater extends Papi_Property {

	/**
	 * Repeater counter number.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	protected $counter = 0;

	/**
	 * Offset columns.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	protected $offset_columns = 0;

	/**
	 * The default value.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $default_value = array();

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
		$result = array();

		if ( ! is_array( $values ) ) {
			$values = array();
		}

		$values_length = count( $values );

		for ( $i = 0; $i < $values_length; $i++ ) {
			$keys   = array_keys( $values[$i] );
			$length = count( $keys );

			for ( $k = 0; $k < $length; $k++ ) {
				if ( $k % 2 !== 0 ) {
					continue;
				}

				$slug = null;
				$type = null;

				if ( isset( $keys[$k + 1] ) && papi_is_property_type_key( $keys[$k + 1] ) ) {
					$slug = $keys[$k];

					if ( isset( $values[ $i ][ $keys[$k + 1] ] ) ) {
						$type = $values[ $i ][ $keys[$k + 1] ];
					}
				}

				if ( empty( $slug ) || empty( $type ) ) {
					continue;
				}

				$property_type = papi_get_property_type( $type );

				if ( empty( $property_type ) ) {
					continue;
				}

				// Format the value from the property class.
				$item = $property_type->format_value( $values[$i][$slug], $slug, $post_id );

				// Apply a filter so this can be changed from the theme for specified property type.
				$item = papi_filter_format_value( $type, $item, $slug, $post_id );

				if ( ! isset( $result[$i] ) ) {
					$result[$i] = array();
				}

				$result[$i][$slug] = $item;
			}
		}

		return $result;
	}

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'items' => array()
		);
	}

	/**
	 * Get number of columns.
	 *
	 * @param int $post_id
	 * @param string $repeater_slug
	 * @param bool $convert
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */

	protected function get_columns( $post_id, $repeater_slug, $convert = true ) {
		$columns = get_post_meta( $post_id, papi_f( papify( $repeater_slug ) . '_columns' ), true );

		if ( $convert ) {
			return intval( $columns );
		}

		return $columns;
	}

	/**
	 * Generate property slug.
	 *
	 * @param object|array $property
	 * @since 1.0.0
	 *
	 * @return string
	 */

	protected function get_property_slug( $property ) {
		$options  = $this->get_options();
		$property = (object) $property;
		return $options->slug . '[' . $this->counter . ']' . '[' . papi_remove_papi( $property->slug ) . ']';
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

	protected function get_results( $value, $repeater_slug, $post_id, $dynamic_columns = false ) {
		global $wpdb;

		$value    = intval( $value );
		$values   = array();
		$table    = $wpdb->prefix . 'postmeta';
		$query    = $wpdb->prepare( "SELECT * FROM `$table` WHERE `meta_key` LIKE '%s' AND `post_id` = %s ORDER BY `meta_id` ASC", $repeater_slug . '_%', $post_id );
		$results  = $wpdb->get_results( $query );
		$trash    = array();
		$trashnum = 0;

		if ( $dynamic_columns && method_exists( $this, 'get_dynamic_columns' ) ) {
			$columns  = $this->get_dynamic_columns( $post_id, $repeater_slug, $results );
		} else {
			$columns  = $this->get_columns( $post_id, $repeater_slug );
		}

		if ( empty( $value ) || empty( $columns ) || empty( $results ) ) {
			return array( array(), array() );
		}

		$values[$repeater_slug] = $value;

		$last_cols = 0;

		for ( $i = 0; $i < $value; $i++ ) {
			if ( $dynamic_columns ) {
				$cols = $columns[$i] + $columns[$i];
			} else {
				$cols = $columns;
			}

			$row = array_slice( $results, $last_cols, $cols );

			$last_cols += $cols;

			$trashnum += count( $row );
			$length = count( $row );

			for ( $j = 0; $j < $length; $j++ ) {
				if ( ! isset( $row[$j] ) ) {
					continue;
				}

				$reg  = '/' . preg_quote( $repeater_slug . '_' . $i . '_' ) . '/';
				$meta = $row[$j];

				if ( empty( $meta ) || ! isset( $meta->meta_value ) || ! preg_match( $reg, $meta->meta_key ) ) {
					continue;
				}

				$property_type_key   = papi_get_property_type_key( $meta->meta_key );
				$property_type_value = get_post_meta( $post_id, papi_f( $property_type_key ), true );

				$meta->meta_value = maybe_unserialize( $meta->meta_value );

				$values[$meta->meta_key] = maybe_unserialize( $meta->meta_value );
				$values[$property_type_key] = $property_type_value;

			}
		}

		if ( count( $results ) > $trashnum ) {
			$trash = array_slice( $results, $trashnum );
		}

		return array( $values, $trash );
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options         = $this->get_options();
		$settings        = $this->get_settings();
		$settings->items = $this->prepare_properties( papi_to_array( $settings->items ) );

		// Reset list counter number.
		$this->counter = 0;

		// Render repeater html.
		$this->render_repeater( $options, $settings );

		// Render JSON template that is used for Papi ajax.
		$this->render_json_template( $options->slug, $settings->items );
	}

	/**
	 * Load value from the database.
	 *
	 * @param mixed $value
	 * @param string $repeater_slug
	 * @param int $post_id
	 * @since 1.1.0
	 */

	public function load_value( $value, $repeater_slug, $post_id ) {
		if ( is_array( $value ) ) {
			return $value;
		}

		list( $results, $trash ) = $this->get_results( $value, $repeater_slug, $post_id );

		// Will not need this array.
		unset( $trash );

		return papi_from_property_array_slugs( $results, $repeater_slug );
	}

	/**
	 * Prepare properties, get properties options object,
	 * check which properties that are allowed to use.
	 *
	 * @param $items
	 * @since 1.0.0
	 *
	 * @return array
	 */

	protected function prepare_properties( $items ) {
		$not_allowed = array( 'repeater' );
		$not_allowed = array_merge( $not_allowed, apply_filters( 'papi/property/repeater/exclude', array() ) );

		$items = array_map( function ( $item ) {

			if ( is_array( $item ) ) {
				return (object) papi_get_property_options( $item, false );
			}

			if ( is_object( $item ) ) {
				return $item;
			}

			return null;

		}, $items );

		return array_filter( $items, function ( $item ) use ( $not_allowed ) {

			if ( ! is_object( $item ) ) {
				return false;
			}

			$type = papi_get_property_short_type( $item->type );

			if ( empty( $type ) ) {
				return false;
			}

			return ! in_array( $type, $not_allowed );
		} );
	}

	/**
	 * Remove all repeater rows from the database.
	 *
	 * @param int $post_id
	 * @param string $repeater_slug
	 * @since 1.1.0
	 */

	protected function remove_repeater_rows( $post_id, $repeater_slug ) {
		global $wpdb;

		$table = $wpdb->prefix . 'postmeta';
		$meta_key = $repeater_slug . '_%';

		// Create sql query and get the results.
		$sql = "SELECT * FROM $table WHERE `post_id` = %d AND (`meta_key` LIKE %s OR `meta_key` LIKE %s AND NOT `meta_key` = %s)";
		$query = $wpdb->prepare( $sql, $post_id, $meta_key, papi_f( $meta_key ), papi_get_property_type_key_f( $repeater_slug ) );
		$results = $wpdb->get_results( $query );

		foreach ( $results as $res ) {
			delete_post_meta( $post_id, $res->meta_key );
		}
	}

	/**
	 * Render extra property html.
	 *
	 * @param string $value_slug
	 * @param array $value
	 * @since 1.3.0
	 */

	protected function render_extra_property_html( $value_slug, $value ) {
	}

	/**
	 * Render property JSON template.
	 *
	 * @param string $slug
	 * @param array $items
	 * @since 1.3.0
	 */

	protected function render_json_template( $slug, $items ) {
		$properties = array();

		foreach ( $items as $key => $value ) {
			$properties[$key] = $value;
			$properties[$key]->raw   = true;
			$properties[$key]->slug  = $this->get_property_slug( $value );
			$properties[$key]->value = '';
		}

		?>
		<script type="application/json" id="<?php echo $slug; ?>_properties_json">
			<?php echo json_encode( $properties ); ?>
		</script>
		<?php
	}

	/**
	 * Render properties.
	 *
	 * @param array $items
	 * @param array $value
	 * @since 1.3.0
	 *
	 * @return bool
	 */

	protected function render_properties( $items, $value ) {
		foreach ( $items as $property ) {
			$render_property = clone $property;
			$value_slug      = papi_remove_papi( $render_property->slug );

			if ( ! array_key_exists( $value_slug, $value ) ) {
				continue;
			}

			$render_property->value = $value[$value_slug];
			$render_property->slug = $this->get_property_slug( $render_property );
			$render_property->raw  = true;

			echo '<td>';
			$this->render_extra_property_html( $value_slug, $value );
			papi_render_property( $render_property );
			echo '</td>';
		}
	}

	/**
	 * Render repeater html.
	 *
	 * @param object $options
	 * @param object $settings
	 * @since 1.3.0
	 */

	protected function render_repeater( $options, $settings ) {
		?>
		<div class="papi-property-repeater" data-json-id="#<?php echo $options->slug; ?>_properties_json">
			<table class="papi-table">
				<thead>
					<tr>
						<th></th>
						<?php foreach ( $settings->items as $property ): ?>
							<th><?php echo $property->title; ?></th>
						<?php endforeach; ?>
						<th class="last"></th>
					</tr>
				</thead>
				<tbody>
					<?php $this->render_repeater_row( $settings->items ); ?>
				</tbody>
			</table>

			<div class="bottom">
				<a href="#" class="button button-primary"><?php _e( 'Add new row', 'papi' ); ?></a>
			</div>

			<?php /* Default repeater value */ ?>

			<input type="hidden" name="<?php echo $options->slug; ?>[]" />

			<?php /* One underscore is saved, two underscores isn't saved */ ?>

			<input type="hidden" name="_<?php echo $options->slug; ?>_columns" value="<?php echo count( $settings->items ); ?>" />
			<?php $values = $this->get_value(); ?>
			<input type="hidden" name="__<?php echo $options->slug; ?>_rows" value="<?php echo count( $values ); ?>" class="papi-property-repeater-rows" />

			<?php
				// this should be rewritten.
				$properties = array_map( function( $item ) {
					$slug = papi_remove_papi( $item->slug );
					$property_type_key = papi_get_property_type_key( $item->slug );
					$property = array();
					$property[$slug] = '';
					$property[$property_type_key] = $item->type;
					return $property;
				}, $settings->items );
			?>

			<input type="hidden" name="__<?php echo $options->slug; ?>_properties" value="<?php echo htmlentities( json_encode( $properties ) ); ?>" />
		</div>
		<?php
	}

	/**
	 * Render repeater row.
	 *
	 * @param array $items
	 * @since 1.3.0
	 */

	protected function render_repeater_row( $items ) {
		$values = $this->get_value();

		// Get all property slugs.
		$slugs = array_map( function ( $item ) {
			return papi_remove_papi( $item->slug );
		}, $items );

		// Match slugs against database values.
		foreach ( $values as $index => $value ) {
			$keys = array_keys( $value );

			foreach ( $slugs as $slug ) {
				if ( in_array( $slug, $keys ) ) {
					continue;
				}

				$values[$index][$slug] = '';
			}
		}

		foreach ( $values as $value ):
			?>
			<tr>
				<td class="handle">
					<span><?php echo $this->counter + 1; ?></span>
				</td>
				<?php
					$this->render_properties( $items, $value );
					$this->counter ++;
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
	 * Update value before it's saved to the database.
	 *
	 * @param mixed $values
	 * @param string $repeater_slug
	 * @param int $post_id
	 *
	 * @since 1.1.0
	 */

	public function update_value( $values, $repeater_slug, $post_id ) {
		$properties_key  = papi_ff( papify( $repeater_slug ) . '_properties' );
		$properties      = array();

		if ( isset( $_POST[$properties_key] ) ) {
			$properties = $_POST[$properties_key];
			$properties = papi_remove_trailing_quotes( $properties );
			$properties = json_decode( $properties );
		}

		$rows_key = papi_ff( papify( $repeater_slug ) . '_rows' );
		$rows     = 0;

		if ( isset( $_POST[$rows_key] ) ) {
			$rows     = $_POST[$rows_key];
			$rows     = intval( $rows );
		}

		if ( ! is_array( $values ) ) {
			$values = array();
		}

		list( $results, $trash ) = $this->get_results( $rows, $repeater_slug, $post_id );

		foreach ( $trash as $index => $meta ) {
			delete_post_meta( $post_id, $meta->meta_key );
		}

		foreach ( $values as $index => $value ) {

			if ( ! is_array( $value ) || ! is_array( $properties ) ) {
				continue;
			}

			$keys = array_keys( $value );

			foreach ( $properties as $empty => $property ) {

				foreach ( $property as $slug => $type ) {
					$slug = papi_remove_papi( $slug );

					if ( in_array( $slug, $keys ) ) {
						$property_type_slug = papi_get_property_type_key( $slug );

						// Run `update_value` on each property before it's saved.
						if ( isset( $values[$index][$property_type_slug] ) ) {
							$property_type = papi_get_property_type( $values[$index][$property_type_slug] );
							$values[$index][$slug] = $property_type->update_value( $values[$index][$slug], $slug, $post_id );
						}

						continue;
					}

					if ( papi_is_property_type_key( $slug ) ) {
						$values[$index][$slug] = $type;
					} else {
						$values[$index][$slug] = '';
					}
				}
			}
		}

		$values = papi_to_property_array_slugs( $values, $repeater_slug );
		$trash  = array_diff( array_keys( papi_to_array( $results ) ), array_keys( papi_to_array( $values ) ) );

		foreach ( $trash as $trash_key => $trash_value ) {
			delete_post_meta( $post_id, $trash_key );
		}

		// Keep this method before the return statement.
		// It's safe to remove all rows in the database here.
		$this->remove_repeater_rows( $post_id, $repeater_slug );

		return $values;
	}
}
