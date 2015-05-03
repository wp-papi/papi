<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Url.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_Url extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'mediauploader' => false
		);
	}

	/**
	 * Display property html.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options  = $this->get_options();
		$settings = $this->get_settings();
		$value    = $this->get_value();

		?>
		<input type="url" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>"
		       class="<?php echo $settings->mediauploader ? 'papi-url-media-input' : ''; ?>"/>

		<?php if ( $settings->mediauploader ): ?>
			&nbsp; <input type="submit" name="<?php echo $options->slug; ?>_button"
			              value="<?php echo __( 'Select file', 'papi' ); ?>" class="button papi-url-media-button"
			              data-papi-action="mediauploader"/>
		<?php endif;
	}

	/**
	 * This filter is applied after the $value is loaded from the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 * @since 1.3.0
	 *
	 * @return mixed
	 */

	public function load_value( $value, $slug, $post_id ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * This filter is applied before the $value is saved in the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 * @since 1.3.0
	 *
	 * @return mixed
	 */

	public function update_value( $value, $slug, $post_id ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}

		return '';
	}

}
