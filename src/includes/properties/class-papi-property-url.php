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
		<input type="url"
			   id="<?php echo $this->html_id(); ?>"
			   name="<?php echo $this->html_name(); ?>"
			   value="<?php echo $this->get_value(); ?>"
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
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}
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
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}
	}

}
