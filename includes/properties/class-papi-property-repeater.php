<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Repeater
 *
 * @package Papi
 * @version 1.0.0
 */
class Papi_Property_Repeater extends Papi_Property {

	/**
	 * List counter number.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	private $counter = 0;

	/**
	 * The default value.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $default_value = array();

	/**
	 * Generate property slug.
	 *
	 * @param object $property
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	private function generate_slug( $property ) {
		$options = $this->get_options();

		return $options->slug . '[' . $this->counter . ']' . '[' . _papi_remove_papi( $property->slug ) . ']';
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
	 * Prepare properties, get properties options object,
	 * check which properties that are allowed to use.
	 *
	 * @param $items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	private function prepare_properties( $items ) {
		$not_allowed = array( 'repeater' );
		$not_allowed = array_merge( $not_allowed, apply_filters( 'papi/property/repeater/not_allowed_properties', array() ) );

		$items = array_map( function ( $item ) {

			if ( is_array( $item ) ) {
				return (object) _papi_get_property_options( $item, false );
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

			return ! in_array( _papi_get_property_short_type( $item->type ), $not_allowed );
		} );
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options         = $this->get_options();
		$settings        = $this->get_settings();
		$values          = $this->get_value();

		$settings->items = $this->prepare_properties( $settings->items );
		?>

		<div class="papi-property-repeater">

			<div class="repeater-template">
				<table class="papi-table papi-table-template">
					<tbody>
					<tr>
						<td>
							<span><?php echo $this->counter + 1; ?></span>
						</td>
						<?php
						foreach ( $settings->items as $property ) {
							$template_property        = clone $property;
							$template_property->raw   = true;
							$template_property->value = '';
							$template_property->slug  = $this->generate_slug( $template_property );
							echo '<td>';
							_papi_render_property( $template_property );
							echo '</td>';
						}
						?>
						<td class="last">
                <span>
                  <a title="<?php _e( 'Remove', 'papi' ); ?>" href="#" class="repeater-remove-item">x</a>
                </span>
						</td>
					</tbody>
				</table>
			</div>

			<table class="papi-table">
				<thead>
				<tr>
					<th></th>
					<?php
					foreach ( $settings->items as $property ) {
						echo '<th>' . $property->title . '</th>';
					}
					?>
					<th class="last"></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $values as $value ): ?>

					<tr>
						<td>
							<span><?php echo $this->counter + 1; ?></span>
						</td>
					<?php

					foreach ( $settings->items as $property ):
						$render_property = clone $property;
						$value_slug      = _papi_remove_papi( $render_property->slug );

						if ( !isset( $value[$value_slug] ) ) {
							continue;
						}

						$render_property->value = $value[$value_slug];
						$render_property->slug = $this->generate_slug( $render_property );
						$render_property->raw  = true;
						?>
							<td>
								<?php _papi_render_property( $render_property ); ?>
							</td>
				<?php
					endforeach;
					$this->counter ++;
					?>

					<td class="last">
						<span>
							<a title="<?php _e( 'Remove', 'papi' ); ?>" href="#" class="repeater-remove-item">x</a>
						</span>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<div class="bottom">
				<a href="#" class="button button-primary"><?php _e( 'Add new row', 'papi' ); ?></a>
			</div>

			<input type="hidden" name="_<?php echo $options->slug; ?>_properties" value="<?php echo count($settings->items); ?>" />

		</div>
	<?php
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param int $post_id
	 * @param string $slug
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function format_value( $values, $slug, $post_id ) {
		$result     = array();

		for( $i = 0; $i < count( $values ); $i++) {
			$keys   = array_keys( $values[$i] );
			$length = count( $keys );

			for ($k = 0; $k < $length; $k++) {
				if ($k % 2 !== 0) {
					continue;
				}

				$slug = null;
				$type = null;

				if ( _papi_is_property_type_key( $keys[$k + 1] ) ) {
					$slug = $keys[$k];

					if ( isset( $values[ $i ][ $keys[$k + 1] ] ) ) {
						$type = $values[ $i ][ $keys[$k + 1] ];
					}
				}

				if ( empty( $slug ) || empty( $type ) ) {
					continue;
				}

				$property_type = _papi_get_property_type( $type );

				if ( empty( $property_type ) ) {
					continue;
				}

				// Format the value from the property class.
				$item = $property_type->format_value( $values[$i][$slug], $slug, $post_id );

				// Apply a filter so this can be changed from the theme for specified property type.
				// Example: "papi/format_value/string"
				$item = _papi_format_value( $type, $item, $slug, $post_id );

				if ( ! isset( $result[$i] ) ) {
					$result[$i] = array();
				}

				$result[$i][$slug] = $item;
			}
		}

		return $result;
	}

	/**
	 * Load value from the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 */

	public function load_value( $value, $slug, $post_id ) {
		global $wpdb;

		if ( is_array( $value ) ) {
			return $value;
		}

		$value         = intval( $value );
		$values        = array();
		$table         = $wpdb->prefix . 'postmeta';
		$query         = $wpdb->prepare( "SELECT * FROM `$table` WHERE `meta_key` LIKE '%s' AND `post_id` = %s ORDER BY `meta_key` ASC", $slug . '_%', $post_id );
		$results       = $wpdb->get_results( $query );
		$properties    = intval( get_post_meta( $post_id, _papi_f( _papify( $slug ) . '_properties' ), true ) );
		$values[$slug] = $value;

		if ( empty( $value ) || empty( $properties ) || empty( $results ) ) {
			return array();
		}

		for ( $i = 0; $i < $value; $i++ ) {

			for( $j = 0; $j < $properties + 1; $j++ ) {

				if ( ! isset( $results[$i + $j] ) ) {
					continue;
				}

				$reg  = '/' . preg_quote( $slug . '_' . $i . '_' ) . '/';
				$meta = $results[$i + $j];

				if ( ! preg_match( $reg, $meta->meta_key ) ) {
					continue;
				}

				$property_type_key   = _papi_get_property_type_key( $meta->meta_key );
				$property_type_value = get_post_meta( $post_id, _papi_f( $property_type_key ), true );

				$values[$meta->meta_key] = $meta->meta_value;
				$values[$property_type_key] = $property_type_value;

			}
		}

		return _papi_from_property_array_slugs( $values, $slug );
	}

	/**
	 * Update value before it's saved to the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 */

	public function update_value( $value, $slug, $post_id ) {
		return _papi_to_property_array_slugs( $value, $slug );
	}

}
