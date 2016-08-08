<?php

/**
 * Bool (true/false) property.
 */
class Papi_Property_Bool extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'bool';

	/**
	 * The default value.
	 *
	 * @var bool
	 */
	public $default_value = false;

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return bool
	 */
	public function format_value( $value, $slug, $post_id ) {
		if ( is_string( $value ) && $value === 'false' || $value === false ) {
			return false;
		}

		return  is_string( $value ) &&
			( $value === 'true' || $value === 'on' ) ||
			$value === true;
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$value = $this->get_value();

		papi_render_html_tag( 'input', [
			'type'  => 'hidden',
			'name'  => esc_attr( $this->html_name() ),
			'value' => false
		] );

		papi_render_html_tag( 'input', [
			'checked' => ! empty( $value ),
			'id'      => esc_attr( $this->html_id() ),
			'name'    => esc_attr( $this->html_name() ),
			'type'    => 'checkbox'
		] );
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
		return $this->format_value( $value, $slug, $post_id );
	}

	/**
	 * Change value after it's loaded from the database.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function load_value( $value, $slug, $post_id ) {
		return is_string( $value ) && $value === '1' || $value;
	}

	/**
	 * Prepare property value.
	 *
	 * @param  mixed $value
	 *
	 * @return mixed
	 */
	protected function prepare_value( $value ) {
		if ( is_string( $value ) && ( $value === 'true' || $value === 'on' ) || $value === true ) {
			return true;
		}

		return null;
	}

	/**
	 * Fix the database value on update.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function update_value( $value, $slug, $post_id ) {
		return $this->prepare_value( $value );
	}
}
