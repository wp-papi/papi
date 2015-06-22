<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Option Type.
 *
 * All option types in the WordPress theme will
 * extend this class.
 *
 * @package Papi
 */

class Papi_Option_Type extends Papi_Page_Type {

	/**
	 * The method name to use instead of `page_type`.
	 *
	 * @var string
	 */

	public $_meta_method = 'option_type';

	/**
	 * The menu to register the option type on.
	 *
	 * @var string
	 */

	public $menu = '';

	/**
	 * The name of the option type.
	 *
	 * @var string
	 */

	public $name = '';

	/**
	 * The fake post type to use.
	 *
	 * @var string
	 */

	public $post_type = '_papi_option_type';

	/**
	 * Get post type.
	 *
	 * @return string
	 */

	public function get_post_type() {
		return $this->post_type[0];
	}

	/**
	 * Render option page type.
	 */

	public function render() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( $this->name ); ?></h2>
			<form id="post" method="post" name="post">
				<div id="papi-hidden-editor" class="hide-if-js">
					<?php wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' ); ?>
					<?php wp_editor( '', 'papiHiddenEditor' ); ?>
				</div>
				<div id="poststuff">
					<div id="post-body">
						<?php do_meta_boxes( $this->post_type[0], 'normal', null ); ?>
						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}
