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
		<style type="text/css">
			#_papi_option_page .inside {
				padding: 0;
				margin-top: -1px;
			}
		</style>

		<div class="wrap">
			<h2>Header</h2>
			<form id="post" method="post" name="post">
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
