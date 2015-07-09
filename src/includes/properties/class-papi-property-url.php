<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Url.
 *
 * @package Papi
 */

class Papi_Property_Url extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'mediauploader' => false
		];
	}

	/**
	 * Display property html.
	 */

	public function html() {
		$settings = $this->get_settings();
		?>
		<input type="url" name="<?php echo $this->html_name(); ?>" value="<?php echo $this->get_value(); ?>"
		       class="<?php echo $settings->mediauploader ? 'papi-url-media-input' : ''; ?>"/>

		<?php if ( $settings->mediauploader ): ?>
			&nbsp; <input type="submit" name="<?php echo $this->html_name(); ?>_button"
			              value="<?php echo __( 'Select file', 'papi' ); ?>" class="button papi-url-media-button"
			              data-papi-action="mediauploader"/>
		<?php endif;
	}

	/**
	 * Change value after it's loaded from the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */

	public function load_value( $value, $slug, $post_id ) {
		$this->update_value( $value, $slug, $post_id );
	}

	/**
	 * Update value before it's saved to the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */

	public function update_value( $value, $slug, $post_id ) {
		$test_value = $value;

		if ( $parts = parse_url( $value ) ) {
			if ( isset( $parts['path'] ) && $ext = pathinfo( $parts['path'], PATHINFO_EXTENSION ) ) {
				$file = basename( $parts['path'] );
				$file = str_replace( '.' . $ext, '', $file );
				$test_value = str_replace( $file, papi_slugify( $file ), $value );
			}
		}

		if ( filter_var( $test_value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}
	}

}
