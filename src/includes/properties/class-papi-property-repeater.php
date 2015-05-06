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
		if ( ! is_array( $values ) ) {
			return array();
		}

		$values = papi_to_property_array_slugs( $values, $repeater_slug );

		foreach ( $values as $slug => $value ) {
			if ( papi_is_property_type_key( $slug ) ) {
				continue;
			}

			$property_type_slug = papi_f( papi_get_property_type_key( $slug ) );

			if ( ! isset( $values[$property_type_slug] ) ) {
				continue;
			}

			// Get property type
			$property_type_value = $values[$property_type_slug];
			$property_type = papi_get_property_type( $property_type_value );

			// Run update value on each property type class.
			$value = $property_type->format_value( $value, $slug, $post_id );

			// Run update value on each property type filter.
			$values[$slug] = papi_filter_format_value( $property_type_value, $value, $slug, $post_id );

			$values[$property_type_slug] = $property_type_value;
		}

		if ( ! is_admin() ) {
			foreach ( $values as $slug => $value ) {
				if ( papi_is_property_type_key( $slug ) ) {
					unset( $values[$slug] );
				}
			}
		}

		return papi_from_property_array_slugs( $values, $repeater_slug );
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
	 * Generate property slug.
	 *
	 * @param object|array $property
	 * @since 1.0.0
	 *
	 * @return string
	 */

	protected function get_property_html_name( $property ) {
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

	protected function get_results( $value, $repeater_slug, $post_id ) {
		global $wpdb;

		$value     = intval( $value );
		$values    = array();
		$table     = $wpdb->prefix . 'postmeta';
		$query     = $wpdb->prepare( "SELECT * FROM `$table` WHERE `meta_key` LIKE '%s' AND `post_id` = %s ORDER BY `meta_id` ASC", $repeater_slug . '_%', $post_id );
		$dbresults = $wpdb->get_results( $query );
		$results   = array();
		$trash     = array();
		$columns   = count( $this->get_settings_properties() );

		// Do not proceed with empty value, columns or dbresults.
		if ( empty( $value ) || empty( $columns ) || empty( $dbresults ) ) {
			return array( array(), array() );
		}

		// Get row results.
		$rows = $this->get_row_results( $dbresults );

		// Add repeater slug with number of rows to the values array.
		$values[$repeater_slug] = $value;

		// Get all properties slugs.
		$slugs = $this->get_settings_properties_slugs();

		for ( $i = 0; $i < $value; $i++ ) {

			$no_trash = array();

			if ( ! isset( $no_trash[$i] ) ) {
				$no_trash[$i] = array();
			}

			for ( $j = 0; $j < $columns; $j++ ) {
				// Generate slug from repeater slug, index and property slug.
				$slug = sprintf( '%s_%d_%s', $repeater_slug, $i, $slugs[$j] );

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
	 * Get row results.
	 *
	 * @param array $dbresults
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_row_results( $dbresults ) {
		$results = array();

		foreach ( $dbresults as $key => $meta ) {
			// Find row index key.
			preg_match( '/^[^\d]*(\d+)/', $meta->meta_key, $matches );

			if ( count( $matches ) < 2 ) {
				continue;
			}

			$i = intval( $matches[1] );

			if ( ! isset( $results[$i] ) ) {
				$results[$i] = array();
			}

			$results[$i][$meta->meta_key] = $meta;
		}

		return $results;
	}

	/**
	 * Get settings properties.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_settings_properties() {
		$settings = $this->get_settings();
		return $this->prepare_properties( papi_to_array( $settings->items ) );
	}

	/**
	 * Get settings properties slugs.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_settings_properties_slugs() {
		$properties = $this->get_settings_properties();
		$slugs = array_map( function( $property ) {
			return papi_remove_papi( $property->slug );
		}, $properties );
		return array_values( $slugs );
	}

	/**
	 * Display property html.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options = $this->get_options();

		// Reset list counter number.
		$this->counter = 0;

		// Render repeater html.
		$this->render_repeater( $options );

		// Render JSON template that is used for Papi ajax.
		$this->render_json_template( $options->slug );
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
		$not_allowed = array( 'repeater', 'flexible' );
		$not_allowed = array_merge( $not_allowed, apply_filters( 'papi/property/repeater/exclude', array() ) );
		$items       = array_map( 'papi_get_property_options', $items );

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
	 * Render property JSON template.
	 *
	 * @param string $slug
	 * @since 1.3.0
	 */

	protected function render_json_template( $slug ) {
		$items = $this->get_settings_properties();
		$properties = array();

		foreach ( $items as $key => $value ) {
			$properties[$key] = $value;
			$properties[$key]->raw   = true;
			$properties[$key]->slug  = $this->get_property_html_name( $value );
			$properties[$key]->value = '';
		}

		?>
		<script type="application/json" data-papi-json="<?php echo $slug; ?>_repeater_json">
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
	 */

	protected function render_properties( $items, $value ) {
		foreach ( $items as $property ) {
			$render_property = clone $property;
			$value_slug      = papi_remove_papi( $render_property->slug );

			if ( ! array_key_exists( $value_slug, $value ) ) {
				continue;
			}

			$render_property->value = $value[$value_slug];
			$render_property->slug = $this->get_property_html_name( $render_property );
			$render_property->raw  = true;

			echo '<td>';
			papi_render_property( $render_property );
			echo '</td>';
		}
	}

	/**
	 * Render repeater html.
	 *
	 * @param object $options
	 * @since 1.3.0
	 */

	protected function render_repeater( $options ) {
		?>
		<div class="papi-property-repeater papi-property-repeater-top" data-slug="<?php echo $options->slug; ?>">
			<table class="papi-table">
				<?php $this->render_repeater_head(); ?>

				<tbody class="repeater-tbody">
					<?php $this->render_repeater_row(); ?>
				</tbody>
			</table>

			<div class="bottom">
				<a href="#" class="button button-primary" data-papi-json="<?php echo $options->slug; ?>_repeater_json"><?php _e( 'Add new row', 'papi' ); ?></a>
			</div>

			<?php /* Default repeater value */ ?>

			<input type="hidden" name="<?php echo $options->slug; ?>[]" />

			<?php /* One underscore is saved, two underscores isn't saved */ ?>

			<?php $values = $this->get_value(); ?>
			<input type="hidden" name="__<?php echo $options->slug; ?>_rows" value="<?php echo count( $values ); ?>" class="papi-property-repeater-rows" />
		</div>
		<?php
	}

	/**
	 * Render repeater head.
	 *
	 * @since 1.3.0
	 */

	protected function render_repeater_head() {
		$properties = $this->get_settings_properties();
		?>
		<thead>
			<tr>
				<th></th>
				<?php foreach ( $properties as $property ): ?>
					<th><?php echo $property->title; ?></th>
				<?php endforeach; ?>
				<th class="last"></th>
			</tr>
		</thead>
		<?php
	}

	/**
	 * Render repeater row.
	 *
	 * @since 1.3.0
	 */

	protected function render_repeater_row() {
		$items  = $this->get_settings_properties();
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
	 * Render repeater row template.
	 *
	 * @since 1.3.0
	 */

	public function render_repeater_row_template() {
		?>
		<script type="text/template" id="tmpl-papi-property-repeater-row">
			<tr>
				<td class="handle">
					<span><%= counter + 1 %></span>
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
	 *
	 * @since 1.3.0
	 */

	protected function setup_actions() {
		add_action( 'admin_head', array( $this, 'render_repeater_row_template' ) );
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
		$rows_key   = papi_ff( papify( $repeater_slug ) . '_rows' );
		$rows       = 0;

		if ( isset( $_POST[$rows_key] ) ) {
			$rows     = $_POST[$rows_key];
			$rows     = intval( $rows );
		}

		if ( ! is_array( $values ) ) {
			$values = array();
		}

		list( $results, $trash ) = $this->get_results( $rows, $repeater_slug, $post_id );

		// Delete trash values.
		foreach ( $trash as $index => $meta ) {
			delete_post_meta( $post_id, $meta->meta_key );
		}

		$values = papi_to_property_array_slugs( $values, $repeater_slug );

		foreach ( $values as $slug => $value ) {
			if ( papi_is_property_type_key( $slug ) ) {
				continue;
			}

			$property_type_slug = papi_f( papi_get_property_type_key( $slug ) );

			if ( ! isset( $values[$property_type_slug] ) ) {
				continue;
			}

			// Get real property slug
			$property_slug = substr( str_replace( $repeater_slug, '', $slug ), 3 );

			// Get property type
			$property_type_value = $values[$property_type_slug]->type;
			$property_type = papi_get_property_type( $property_type_value );

			// Run update value on each property type class.
			$value = $property_type->update_value( $value, $property_slug, $post_id );

			// Run update value on each property type filter.
			$values[$slug] = papi_filter_update_value( $property_type_value, $value, $property_slug, $post_id );

			$values[$property_type_slug] = $property_type_value;
		}

		$trash  = array_diff( array_keys( papi_to_array( $results ) ), array_keys( papi_to_array( $values ) ) );

		// Delete trash values.
		foreach ( $trash as $trash_key => $trash_value ) {
			delete_post_meta( $post_id, $trash_key );
		}

		// Keep this method before the return statement.
		// It's safe to remove all rows in the database here.
		$this->remove_repeater_rows( $post_id, $repeater_slug );

		return $values;
	}
}
