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

	private $layout_prefix_regex = '/^\_papi\_layout\_/';

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

		$layouts = $values;

		// Find which layout a property belonging to.
		foreach ( $layouts as $index => $layout ) {
			foreach ( $layout as $key => $val ) {
				if ( is_string( $val ) && preg_match( $this->layout_prefix_regex, $val ) ) {
					$item = array();
					$item[$this->layout_key] = $val;
					$layouts[$index] = $item;
				} else {
					unset( $layouts[$index][$key] );
				}
			}

			if ( empty( $layouts[$index] ) ) {
				unset( $layouts[$index] );
			}
		}

		// Get all repeater values.
		$results = parent::format_value( $values, $repeater_slug, $post_id );

		foreach ( $results as $index => $arr ) {
			if ( isset( $layouts[$index] ) ) {
				$results[$index] = array_merge( $results[$index], $layouts[$index] );
			} else {
				unset( $results[$index] );
			}
		}

		// Remove layout prefix when returning result to theme
		if ( ! is_admin () ) {
			foreach ( $results as $index => $row ) {
				$results[$index][$this->layout_key] = preg_replace( $this->layout_prefix_regex, '', $row[$this->layout_key] );
			}
		}

		return $results;
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

	protected function get_layout_slug( $key, $extra = '' ) {
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
		return sprintf( '_papi_%s_%s', $prefix, $name );
	}

	/**
	 * Get columns for layouts.
	 *
	 * @param int $post_id
	 * @param string $repeater_slug
	 * @param array $dbresults
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_dynamic_columns( $post_id, $repeater_slug, $dbresults ) {
		$columns = parent::get_settings_properties();

		foreach ( $columns as $index => $layout ) {
			$key = $this->get_layout_value( 'layout', $layout['slug'] );
			unset( $columns[$index] );
			$columns[$key] = count( $layout['items'] );
		}

		$results = array();
		$layout_key_regex = $this->layout_key_regex;

		$layouts = array_values( array_filter( $dbresults, function ( $row ) use ($layout_key_regex) {
			return preg_match( $this->layout_key_regex, $row->meta_key );
		} ) );

		foreach ( $layouts as $index => $row ) {
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
	 * Get settings properties.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	protected function get_settings_properties() {
		$settings = $this->get_settings();
		$items    = $settings->items;
		$items    = array_map( function ( $layout ) {
			return $layout['items'];
		}, $items );
		return $this->prepare_properties( papi_to_array( $items ) );
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
		foreach ( $items as $index => $layout ) {
			if ( ! $this->valid_layout( $layout ) ) {
				unset( $items[$index] );
				continue;
			}

			if ( ! isset( $layout['slug'] ) ) {
				$layout['slug'] = $layout['title'];
			}

			$items[$index]['slug']  = papi_slugify( $layout['slug'] );
			$items[$index]['items'] = parent::prepare_properties( $layout['items'] );
		}

		return $items;
	}

	/**
	 * Render layout input.
	 *
	 * @param string $slug
	 * @param string $value
	 * @since 1.3.0
	 */

	protected function render_layout_input( $slug, $value ) {
		$slug = $this->get_property_slug( array(
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
				$properties[$key]->slug  = $this->get_property_slug( $value );
				$properties[$key]->value = '';
			}
			?>

			<script type="application/json" data-papi-json="<?php echo $this->get_layout_slug( $layout['title'], 'flexible_json' ); ?>">
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

			<div class="flexible-layouts papi-hide">
				<div class="flexible-layouts-arrow"></div>
				<ul>
					<?php foreach ( $items as $layout ): ?>
						<li data-papi-json="<?php echo $options->slug; ?>_<?php echo $layout['slug']; ?>_flexible_json"><?php echo $layout['title']; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="bottom">
				<a href="#" class="button button-primary"><?php _e( 'Add new row', 'papi' ); ?></a>
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
	 * @since 1.3.0
	 */

	protected function render_repeater_row() {
		$items   = parent::get_settings_properties();
		$values  = $this->get_value();

		// Get all property slugs.
		$slugs = array_map( function ( $layout ) {
			return array_map( function ( $item ) {
				return papi_remove_papi( $item->slug );
			}, $layout['items'] );
		}, $items );

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
