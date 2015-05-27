<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Repeater.
 *
 * @package Papi
 */

class Papi_Property_Repeater extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */

	public $convert_type = 'array';

	/**
	 * Repeater counter number.
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

	protected $exclude_properties = ['repeater'];

	/**
	 * Format the value of the property before it's returned to the theme.
	 *
	 * @param mixed $values
	 * @param string $repeater_slug
	 * @param int $post_id
	 *
	 * @return array
	 */

	public function format_value( $values, $repeater_slug, $post_id ) {
		if ( ! is_array( $values ) ) {
			return [];
		}

		$values = papi_to_property_array_slugs( $values, $repeater_slug );

		foreach ( $values as $slug => $value ) {
			if ( papi_is_property_type_key( $slug ) ) {
				continue;
			}

			$property_type_slug = papi_get_property_type_key_f( $slug );

			if ( ! isset( $values[$property_type_slug] ) ) {
				continue;
			}

			// Get property type
			$property_type_value = $values[$property_type_slug];
			$property_type = papi_get_property_type( $property_type_value );

			if ( ! is_object( $property_type ) ) {
				continue;
			}

			// Run update value on each property type class.
			$values[$slug] = $property_type->format_value( $value, $slug, $post_id );

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
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'items' => []
		];
	}

	/**
	 * Get JSON property that is used when adding new row.
	 *
	 * @return object
	 */

	protected function get_json_property( $property ) {
		$property = papi_get_property_type( $property );

		if ( ! papi_is_property( $property ) ) {
			return (object) [];
		}

		$options = $property->get_options();
		$options->slug  = $options->array_slug;
		$options->raw   = true;
		$options->slug  = $this->html_name( $options, $this->counter );
		$options->value = '';
		return $options;
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

		// Do not proceed with empty value or dbresults.
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
		$values[$repeater_slug] = $value;

		for ( $i = 0; $i < $value; $i++ ) {

			$no_trash = [];

			if ( ! isset( $no_trash[$i] ) ) {
				$no_trash[$i] = [];
			}

			if ( ! isset( $rows[$i] ) ) {
				continue;
			}

			foreach ( $rows[$i] as $slug => $meta ) {
				if ( ! is_string( $slug ) || ! isset( $rows[$i][$slug] ) ) {
					continue;
				}

				// Add meta object to the no trash array.
				// so it won't be deleted.
				$no_trash[$slug] = $meta;

				// Serialize value if needed.
				$meta->meta_value = maybe_unserialize( $meta->meta_value );

				// Add property value and property type value.
				$values[$meta->meta_key] = maybe_unserialize( $meta->meta_value );

				// Add the meta value.
				$values[$meta->meta_key] = $rows[$i][$slug]->meta_value;
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

		return [$values, $trash];
	}

	/**
	 * Get row results.
	 *
	 * @param array $dbresults
	 *
	 * @return array
	 */

	protected function get_row_results( $dbresults ) {
		$results     = [];
		$option_page =  $this->is_option_page();

		foreach ( $dbresults as $key => $meta ) {

			if ( $option_page ) {
				preg_match( '/^[^\d]*(\d+)/', $meta->option_name, $matches );
			} else {
				preg_match( '/^[^\d]*(\d+)/', $meta->meta_key, $matches );
			}

			if ( count( $matches ) < 2 ) {
				continue;
			}
			$i = intval( $matches[1] );

			if ( ! isset( $results[$i] ) ) {
				$results[$i] = [];
			}

			if ( $option_page ) {
				$results[$i][$meta->option_name] = (object) [
					'meta_key'   => $meta->option_name,
					'meta_value' => $meta->option_value
				];
			} else {
				$results[$i][$meta->meta_key] = $meta;
			}
		}

		return $results;
	}

	/**
	 * Get settings properties.
	 *
	 * @return array
	 */

	protected function get_settings_properties() {
		$settings = $this->get_settings();

		if ( is_null( $settings ) ) {
			return [];
		}

		return $this->prepare_properties( papi_to_array( $settings->items ) );
	}

	/**
	 * Display property html.
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
	 * Change value after it's loaded from the database
	 * and populate every property in the repeater with the right property type.
	 *
	 * @param mixed $value
	 * @param string $repeater_slug
	 * @param int $post_id
	 */

	public function load_value( $value, $repeater_slug, $post_id ) {
		if ( is_array( $value ) ) {
			return $value;
		}

		list( $results, $trash ) = $this->get_results( $value, $repeater_slug, $post_id );

		// Will not need this array.
		unset( $trash );

		$results   = papi_from_property_array_slugs( $results, papi_remove_papi( $repeater_slug ) );
		$data_page = $this->get_page();
		$types     = [];

		if ( empty( $data_page ) || empty( $results ) ) {
			return $this->default_value;
		}

		foreach ( $results[0] as $slug => $value ) {
			if ( $property = $data_page->get_property_from_page_type( $repeater_slug, $slug ) ) {
				$types[$slug] = $property;
			}
		}

		foreach ( $results as $index => $row ) {
			foreach ( $row as $slug => $value ) {
				if ( ! isset( $types[$slug] ) ) {
					continue;
				}

				$type_key = papi_get_property_type_key_f( $slug );
				$results[$index][$type_key] = $types[$slug];
			}
		}

		return $results;
	}

	/**
	 * Prepare properties, get properties options object,
	 * check which properties that are allowed to use.
	 *
	 * @param $items
	 *
	 * @return array
	 */

	protected function prepare_properties( $items ) {
		$exclude_properties = $this->exclude_properties;
		$exclude_properties = array_merge( $exclude_properties, apply_filters( 'papi/property/repeater/exclude', array() ) );
		$items       = array_map( 'papi_get_property_options', $items );

		return array_filter( $items, function ( $item ) use ( $exclude_properties ) {

			if ( ! is_object( $item ) ) {
				return false;
			}

			if ( empty( $item->type ) ) {
				return false;
			}

			return ! in_array( $item->type, $exclude_properties );
		} );
	}

	/**
	 * Remove all repeater rows from the database.
	 *
	 * @param int $post_id
	 * @param string $repeater_slug
	 */

	protected function remove_repeater_rows( $post_id, $repeater_slug ) {
		global $wpdb;

		$option_page   = $this->is_option_page();
		$repeater_slug = $repeater_slug . '_%';

		if ( $option_page ) {
			$table = $wpdb->prefix . 'options';
			$sql   = "SELECT * FROM $table WHERE (`option_name` LIKE %s OR `option_name` LIKE %s AND NOT `option_name` = %s)";
			$query = $wpdb->prepare( $sql, $repeater_slug, papi_f( $repeater_slug ), papi_get_property_type_key_f( $repeater_slug ) );
		} else {
			$table = $wpdb->prefix . 'postmeta';
			$sql   = "SELECT * FROM $table WHERE `post_id` = %d AND (`meta_key` LIKE %s OR `meta_key` LIKE %s AND NOT `meta_key` = %s)";
			$query = $wpdb->prepare( $sql, $post_id, $repeater_slug, papi_f( $repeater_slug ), papi_get_property_type_key_f( $repeater_slug ) );
		}

		$results = $wpdb->get_results( $query );

		foreach ( $results as $res ) {
			if ( $option_page ) {
				delete_option( $res->option_name );
			} else {
				delete_post_meta( $post_id, $res->meta_key );
			}
		}
	}

	/**
	 * Render property JSON template.
	 *
	 * @param string $slug
	 */

	protected function render_json_template( $slug ) {
		$items = $this->get_settings_properties();
		$properties = [];

		foreach ( $items as $key => $value ) {
			$properties[$key] = $this->get_json_property( $value );
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
	 * @param array $row
	 * @param array $value
	 */

	protected function render_properties( $row, $value ) {
		foreach ( $row as $property ) {
			$render_property = $property->get_options();
			$value_slug      = papi_remove_papi( $render_property->array_slug );

			if ( ! array_key_exists( $value_slug, $value ) ) {
				continue;
			}

			$render_property->value = $value[$value_slug];
			$render_property->slug  = $this->html_name( $property, $this->counter );
			$render_property->raw   = true;

			echo '<td>';
			papi_render_property( $render_property );
			echo '</td>';
		}
	}

	/**
	 * Render repeater html.
	 *
	 * @param object $options
	 */

	protected function render_repeater( $options ) {
		?>
		<div class="papi-property-repeater papi-property-repeater-top" data-slug="<?php echo $options->slug; ?>">
			<table class="papi-table">
				<?php $this->render_repeater_head(); ?>

				<tbody class="repeater-tbody">
					<?php $this->render_repeater_rows(); ?>
				</tbody>
			</table>

			<div class="bottom">
				<a href="#" class="button button-primary" data-papi-json="<?php echo $options->slug; ?>_repeater_json"><?php _e( 'Add new row', 'papi' ); ?></a>
			</div>

			<?php /* Default repeater value */ ?>

			<input type="hidden" name="<?php echo $options->slug; ?>[]" />

			<?php $values = $this->get_value(); ?>

			<input type="hidden" name="__<?php echo $options->slug; ?>_rows" value="<?php echo count( $values ); ?>" class="papi-property-repeater-rows" />
		</div>
		<?php
	}

	/**
	 * Render repeater head.
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
	 * Render repeater rows.
	 */

	protected function render_repeater_rows() {
		$items  = $this->get_settings_properties();
		$values = $this->get_value();

		$slugs = array_map( function ( $item ) {
			return papi_remove_papi( $item->slug );
		}, $items );

		// Remove values that don't exists in the slugs array.
		foreach ( $values as $index => $value ) {
			$keys = array_keys( $value );

			foreach ( $slugs as $slug ) {
				if ( in_array( $slug, $keys ) ) {
					continue;
				}

				$values[$index][$slug] = '';
			}
		}

		$values = array_filter( $values );

		foreach ( $values as $row ):
			?>
			<tr>
				<td class="handle">
					<span><?php echo $this->counter + 1; ?></span>
				</td>
				<?php
					$this->render_properties( $items, $row );
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

	public function render_repeater_rows_template() {
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
	 */

	protected function setup_actions() {
		add_action( 'admin_head', [$this, 'render_repeater_rows_template'] );
	}

	/**
	 * Update value before it's saved to the database.
	 *
	 * @param mixed $values
	 * @param string $repeater_slug
	 * @param int $post_id
	 */

	public function update_value( $values, $repeater_slug, $post_id ) {
		$rows_key   = papi_ff( papify( $repeater_slug ) . '_rows' );
		$rows       = 0;

		if ( isset( $_POST[$rows_key] ) ) {
			$rows     = $_POST[$rows_key];
			$rows     = intval( $rows );
		}

		if ( ! is_array( $values ) ) {
			$values = [];
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

			$property_type_slug = papi_get_property_type_key_f( $slug );

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

			if ( isset( $values[$property_type_slug] ) ) {
				unset( $values[$property_type_slug] );
			}
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
