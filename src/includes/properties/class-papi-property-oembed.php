<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property oEmbed.
 *
 * @package Papi
 * @since 1.3.0
 */

class Papi_Property_Oembed extends Papi_Property {

	/**
	 * The default value.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $default_value = '';

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( is_admin() ) {
			return $value;
		} else {
			return $this->get_oembed_html( $value );
		}
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
			'force_size' => 'admin', // or 'both'
			'height'     => 390,
			'width'      => 640
		);
	}

	/**
	 * Get oEmbed html.
	 *
	 * @param string $url
	 * @param mixed $width
	 * @param mixed $height
	 * @since 1.3.0
	 *
	 * @return string
	 */

	public function get_oembed_html( $url, $width = null, $height = null ) {
		$settings = $this->get_settings();
		$width = intval( empty( $width ) ? $settings->width : $width );
		$height = intval( empty( $height ) ? $settings->height : $height );

		$html = wp_oembed_get( $url, array(
			'width'  => $width,
			'height' => $height
		) );

		$force_size = $settings->force_size === 'admin' ?
			is_admin() : $settings->force_size === 'both';

		if ( $force_size ) {
			$html = preg_replace( '/height=\"\d+\"/', 'height="' . $height .'"', $html );
			$html = preg_replace( '/width=\"\d+\"/', 'width="' . $width .'"', $html );
		}

		return $html;
	}

	/**
	 * Handle oEmbed ajax request.
	 *
	 * @since 1.3.0
	 */

	public function handle_oembed_ajax() {
		$width  = papi_get_qs( 'width' );
		$height = papi_get_qs( 'height' );
		$url    = papi_get_qs( 'url' );
		$html   = $this->get_oembed_html( $url, $width, $height );

		header( 'Content-Type: application/json' );

		if ( $html ) {
			echo json_encode( array( 'success' => true, 'html' => $html ) );
		} else {
			echo json_decode( array( 'success' => false, 'error' => '' ) );
		}

		exit;
	}

	/**
	 * Display property html.
	 *
	 * @since 1.3.0
	 */

	public function html() {
		$settings = $this->get_settings();
		$slug     = $this->get_option( 'slug' );
		$value    = $this->get_value();
		$html     = $this->get_oembed_html( $value );
		?>
		<div class="papi-property-oembed">
			<div class="papi-oembed-top">
				<input type="text" name="<?php echo $slug; ?>" value="<?php echo $value; ?>" />
			</div>
			<div class="papi-oembed-bottom <?php echo empty( $html ) ? 'loading' : ''; ?>" style="height:<?php echo $settings->height; ?>px;width:<?php echo $settings->width; ?>px;">
				<?php echo $html; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * This filter is applied after the $value is loaded from the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 * @since 1.3.0
	 *
	 * @return string
	 */

	public function load_value( $value, $slug, $post_id ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.3.0
	 */

	protected function setup_actions() {
		add_action( 'papi_ajax_handle_oembed_ajax', array( $this, 'handle_oembed_ajax' ) );
	}

	/**
	 * This filter is applied before the $value is saved in the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 * @since 1.3.0
	 *
	 * @return string
	 */

	public function update_value( $value, $slug, $post_id ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}

		return '';
	}

}
