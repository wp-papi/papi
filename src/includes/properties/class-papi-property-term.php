<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Term class.
 *
 * @package Papi
 */
class Papi_Property_Term extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'object';

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'taxonomy'     => '',
			'select2'       => true,
			'query'         => []
		];
	}

	/**
	 * Get terms.
	 *
	 * @param  stdClass $settings
	 *
	 * @return array
	 */
	protected function get_terms( $settings ) {
		// Prepare arguments for get_terms.
		$args = array_merge( $settings->query, [
			'fields' => 'id=>name'
		] );

		$terms = get_terms( $settings->taxonomy, $args );

		return $terms;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings   = $this->get_settings();
		$value      = $this->get_value();

		if ( is_object( $value ) ) {
			$value = $value->term_id;
		} else {
			$value = 0;
		}

		$terms        = $this->get_terms( $settings );
		$classes      = 'papi-fullwidth';

		if ( $settings->select2 ) {
			$classes = ' papi-component-select2';
		}
		?>

		<div class="papi-property-term">
			<select
				id="<?php echo $this->html_id(); ?>"
				name="<?php echo $this->html_name(); ?>"
				class="<?php echo $classes; ?>"
				data-allow-clear="true"
				data-placeholder="<?php echo isset( $settings->placeholder ) ? $settings->placeholder : ''; ?>"
				data-width="100%">

				<?php if ( isset( $settings->placeholder ) ): ?>
					<option value=""></option>
				<?php endif; ?>

				<?php foreach ( $terms as $term_id => $term_name ) : ?>
					<?php
					papi_render_html_tag( 'option', [
						'value'    => $term_id,
						'selected' => $value === $term_id ? 'selected' : null,
						$term_name
					] );
					?>
				<?php endforeach; ?>

			</select>
		</div>
		<?php
	}

	/**
	 * Import value to the property.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( is_object( $value ) && isset( $value->term_id ) ) {
			return $value->term_id;
		}

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		return $this->default_value;
	}

	/**
	 * Format the value of the property before it's returned to the application.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_numeric( $value ) && intval( $value ) !== 0 ) {
			$settings = $this->get_settings();
			return get_term( $value, $settings->taxonomy );
		}

		return $this->default_value;
	}
}
