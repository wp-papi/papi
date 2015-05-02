<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Property Flexible
 *
 * @package Papi
 * @version 1.0.0
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
	 * Calculate dynamic columns or not?
	 *
	 * @var bool
	 * @since 1.3.0
	 */

	protected $dynamic_columns = true;

	/**
	 * Group prefix regex.
	 *
	 * @var string
	 * @since 1.3.0
	 */

	private $group_prefix_regex = '/^\_papi\_group\_/';

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $values
	 * @param string $repeater_slug
	 * @param int $post_id
	 * @since 1.3.0
	 *
	 * @return array
	 */

	public function format_value( $values, $repeater_slug, $post_id ) {
		if ( ! is_array( $values ) ) {
			$values = array();
		}

		$groups = $values;

		// Find which group a property belonging to.
		foreach ( $groups as $index => $group ) {
			foreach ( $group as $key => $val ) {
				if ( is_string( $val ) && preg_match( $this->group_prefix_regex, $val ) ) {
					$group = array();
					$key = papify( 'group' );
					$group[$key] = $val;
					$groups[$index] = $group;
				} else {
					unset( $groups[$index][$key] );
				}
			}

			if ( empty( $groups[$index] ) ) {
				unset( $groups[$index] );
			}
		}

		// Get all repeater values.
		$results = parent::format_value( $values, $repeater_slug, $post_id );

		foreach ( $results as $index => $arr ) {
			if ( isset( $groups[$index] ) ) {
				$results[$index] = array_merge( $results[$index], $groups[$index] );
			} else {
				unset( $results[$index] );
			}
		}

		return $results;
	}

	/**
	 * Get colspan number for groups.
	 *
	 * @param array $groups
	 * @since 1.3.0
	 *
	 * @return int
	 */

	private function get_colspan_number( $groups ) {
		$colspan = 1;

		foreach ( $groups as $group ) {
			$size = count( $group );

			if ( $size > $colspan ) {
				$colspan = $size;
			}
		}

		return $colspan;
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
	 * Generate group slug.
	 *
	 * @param string $key
	 * @param string $extra
	 * @since 1.3.0
	 *
	 * @return string
	 */

	protected function get_group_slug( $key, $extra = '' ) {
		$options = $this->get_options();
		return $options->slug . '_' . papi_slugify( $key ) . ( empty( $extra ) ? '' : '_' . $extra );
	}

	/**
	 * Get item slug.
	 *
	 * @param string $prefix
	 * @param string $name
	 * @since 1.3.0
	 *
	 * @return string
	 */

	protected function get_item_slug( $prefix, $name ) {
		return sprintf( '_papi_%s_%s', $prefix, $name );
	}

	/**
	 * Get columns for groups.
	 *
	 * @param int $post_id
	 * @param string $repeater_slug
	 * @param array $results
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_dynamic_columns( $post_id, $repeater_slug, $dbresults ) {
		$columns = $this->get_settings_properties();

		foreach ( $columns as $group_id => $group ) {
			$key = $this->get_item_slug( 'group', $group_id );
			unset( $columns[$group_id] );
			$columns[$key] = count( $group );
		}

		$results = array();

		$groups = array_values( array_filter( $dbresults, function ( $row ) {
			return preg_match( '/\_group$/', $row->meta_key );
		} ) );

		foreach ( $groups as $index => $row ) {
			$pattern = '/^' . str_replace( '_', '\_', $repeater_slug ) . '\_\d+/';
			preg_match( $pattern, $row->meta_key, $matches );

			if ( empty( $matches ) ) {
				continue;
			}

			$key = $matches[0];

			if ( isset( $results[$key] ) ) {
				continue;
			}

			$colkey = $row->meta_value;
			$results[$key] = $columns[$colkey];
		}

		return array_values( $results );
	}

	/**
	 * Prepare properties, get properties options object,
	 * check which properties that are allowed to use.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function prepare_properties( $items ) {
		foreach ( $items as $key => $group ) {
			$items[$key] = parent::prepare_properties( $group );
		}

		return $items;
	}

	/**
	 * Render group input.
	 *
	 * @param string $slug
	 * @param string $value
	 * @since 1.3.0
	 */

	protected function render_group_input( $slug, $value ) {
		$slug = $this->get_property_slug( array(
			'slug' => $slug . '_group'
		) );
		?>
		<input type="hidden" name="<?php echo $slug; ?>" value="<?php echo $value; ?>" />
		<?php
	}

	/**
	 * Render group JSON template.
	 *
	 * @param string $slug
	 * @param array $items
	 * @since 1.3.0
	 */

	protected function render_json_template( $slug ) {
		$items = $this->get_settings_properties();
		$colspan = $this->get_colspan_number( $items );
		$index = 0;

		foreach ( $items as $name => $group ):
			$properties = array();

			foreach ( $group as $key => $value ) {
				$properties[$key] = $value;
				$properties[$key]->raw   = true;
				$properties[$key]->slug  = $this->get_property_slug( $value );
				$properties[$key]->value = '';
			}
			?>

			<script type="application/json" data-papi-json="<?php echo $this->get_group_slug( $name, 'flexible_json' ); ?>">
				<?php echo json_encode( array(
						'colspan'    => $colspan,
						'columns'    => $this->get_item_slug( 'columns', count( $group ) ),
						'group'      => $this->get_item_slug( 'group', $name ),
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
	 *
	 * @return bool
	 */

	protected function render_properties( $items, $value, $colspan = null ) {
		?>
			<td class="flexible-td">
				<table class="flexible-table">
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
			$render_property->slug = $this->get_property_slug( $render_property );
			$render_property->raw  = true;

			if ( $i == $l - 1 ) {
				echo '<td class="flexible-td-last">';
			} else {
				echo '<td>';
			}

			$this->render_group_input( $value_slug, $value['papi_group'] );
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
	 * Get group settings.
	 *
	 * @param string $group
	 * @since 1.3.0
	 *
	 * @return object
	 */

	protected function get_group_settings( $group ) {
		$settings = $this->get_settings();
		$defaults = array(
			'title' => sprintf("%s %s", __('Add New', 'papi'), ucfirst( $group ) )
		);

		if ( isset( $settings->groups[$group] ) ) {
			return (object) $settings->groups[$group];
		}

		return (object) $defaults;
	}

	/**
	 * Render repeater html.
	 *
	 * @param object $options
	 * @param object $settings
	 * @since 1.3.0
	 */

	protected function render_repeater( $options ) {
		$items = $this->get_settings_properties();
		?>

		<div class="papi-property-flexible">
			<table class="papi-table">
				<tbody class="repeater-tbody">
					<?php $this->render_repeater_row(); ?>
				</tbody>
			</table>

			<div class="bottom">
				<?php foreach ( $items as $key => $group ): ?>
				<?php $settings = $this->get_group_settings($key); ?>
					<a href="#" class="button button-primary" data-papi-json="<?php echo $options->slug; ?>_<?php echo papi_slugify( $key ); ?>_flexible_json"><?php echo $settings->title; ?></a>
				<?php endforeach; ?>
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
	 * Render repeater row.
	 *
	 * @param array $items
	 * @since 1.3.0
	 */

	protected function render_repeater_row() {
		$items   = $this->get_settings_properties();
		$values  = $this->get_value();
		$colspan = $this->get_colspan_number( $items );

		// Get all property slugs.
		$slugs = array_map( function ( $group ) {
			return array_map( function ( $item ) {
				return papi_remove_papi( $item->slug );
			}, $group );
		}, $items );

		// Match slugs against database values.
		foreach ( $values as $index => $value ) {
			$keys = array_keys( $value );
			foreach ( $slugs as $group ) {
				foreach ( $group as $slug ) {
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
					foreach ( $items as $name => $group ) {
						// Don't render groups that don't have a valid value in the database.
						if ( ! isset( $value['papi_group'] ) || $this->get_item_slug( 'group', $name ) !== $value['papi_group'] ) {
							continue;
						}

						// Render all properties in the group
						$this->render_properties( $group, $value, $colspan );
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

}
