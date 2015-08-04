<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Property Link class.
 *
 * @package Papi
 */
class Papi_Property_Link extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'object';

	/**
	 * The default value.
	 *
	 * @var array
	 */
	public $default_value = [];

	/**
	 * Delete value from the database.
	 *
	 * @param string $slug
	 * @param int $post_id
	 * @param string $type
	 *
	 * @return bool
	 */
	public function delete_value( $slug, $post_id, $type ) {
		$values = $this->load_value( null, $slug, $post_id );
		$result = true;

		foreach ( $values as $key => $val ) {
			$out    = delete_post_meta( $post_id, $slug . '_' . $key );
			$result = $out ? $result : $out;
		}

		if ( $result ) {
			$result = delete_post_meta( $post_id, $slug );
		}

		return $result;
	}

	/**
	 * Format the value of the property before it's returned to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function format_value( $value, $slug, $post_id ) {
		return $this->load_value( $value, $slug, $post_id );
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [];
	}

	/**
	 * Unserialize value from database.
	 *
	 * @param string $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function load_value( $value, $slug, $post_id ) {
		$values = [
			'url',
			'title',
			'target'
		];

		foreach ( $values as $index => $key ) {
			$values[$key] = get_post_meta( $post_id, $slug . '_' . $key, true );
			unset( $values[$index] );
		}

		return (object) $values;
	}

	/**
	 * Display property html.
	 */
	public function html() {
		$value = $this->get_value();

		if ( ! is_array( $value ) && ! is_object( $value ) ) {
			$value = [];
		}

		$value = (object) $value;
		?>

		<div class="papi-property-link" data-slug="<?php echo $this->html_name(); ?>">
			<?php if ( isset( $value->url ) ): ?>
				<table class="papi-table link-table">
					<tbody>
						<tr>
							<td>
								<?php _e( 'URL', 'papi' ); ?>
							</td>
							<td>
								<a href="<?php echo $value->url; ?>" target="_blank"><?php echo $value->url; ?></a>
								<input type="hidden" value="<?php echo $value->title . ' - ' . $value->url; ?>" data-papi-rule="<?php echo $this->html_name(); ?>">
								<input class="wp-link-url" type="hidden" value="<?php echo $value->url; ?>" name="<?php echo $this->html_name(); ?>[url]">
							</td>
						</tr>
						<tr>
							<td>
								<?php _e( 'Title', 'papi' ); ?>
							</td>
							<td>
								<?php echo $value->title; ?>
								<input class="wp-link-text" type="hidden" value="<?php echo $value->title; ?>" name="<?php echo $this->html_name(); ?>[title]">
							</td>
						</tr>
						<tr>
							<td>
								<?php _e( 'Target', 'papi' ); ?>
							</td>
							<td>
								<?php echo $value->target === '_blank' ? __( 'New window', 'papi' ) : __( 'Same window', 'papi' ); ?>
								<input class="wp-link-target" type="hidden" value="<?php echo $value->target; ?>" name="<?php echo $this->html_name(); ?>[target]">
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

			<p class="papi-file-select">
				<span class="<?php echo isset( $value->url ) ? 'papi-hide' : ''; ?>">
					<?php _e( 'No link selected', 'papi' ); ?>
					<button class="button" data-link-action="add"><?php _e( 'Add link', 'papi' ); ?></button>
				</span>
				<span class="<?php echo isset( $value->url ) ? '' : 'papi-hide'; ?>">
					<button class="button" data-link-action="edit"><?php _e( 'Edit link', 'papi' ); ?></button>
					<button class="button" data-link-action="remove"><?php _e( 'Remove link', 'papi' ); ?></button>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Render link template.
	 */
	public function render_link_template() {
		?>
		<script type="text/template" id="tmpl-papi-property-link">
			<table class="papi-table link-table">
				<tbody>
					<tr>
						<td>
							<?php _e( 'URL', 'papi' ); ?>
						</td>
						<td>
							<%= link %>
							<input type="hidden" value="<%= title %> - <%= href %>" data-papi-rule="<%= slug %>">
							<input class="wp-link-url" type="hidden" value="<%= href %>" name="<%= slug %>[url]">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Title', 'papi' ); ?>
						</td>
						<td>
							<%= title %>
							<input class="wp-link-text" type="hidden" value="<%= title %>" name="<%= slug %>[title]">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Target', 'papi' ); ?>
						</td>
						<td>
							<input class="wp-link-target" type="hidden" value="<%= target %>" name="<%= slug %>[target]">
							<%= target === '_blank' ? '<?php _e( 'New window', 'papi' ) ?>' : '<?php _e( 'Same window', 'papi' ); ?>' %>
						</td>
					</tr>
				</tbody>
			</table>
		</script>
		<?php
	}

	/**
	 * Prepare value for the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function update_value( $values, $slug, $post_id ) {
		foreach ( $values as $key => $val ) {
			$values[$slug . '_' . $key] = $val;
			unset( $values[$key] );
		}

		$values[$slug] = 1;

		return $values;
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_head', [$this, 'render_link_template'] );
	}

}
