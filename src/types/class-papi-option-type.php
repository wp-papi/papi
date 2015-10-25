<?php

/**
 * Papi type that handle option, option data
 * and rendering. All option types should extend this
 * class.
 */
class Papi_Option_Type extends Papi_Page_Type {

	/**
	 * The method name to use instead of `page_type`.
	 *
	 * @var string
	 */
	public $_meta_method = 'option_type';

	/**
	 * Capability.
	 *
	 * @var array
	 */
	public $capability = 'manage_options';

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
			<h2><?php echo esc_html( $this->name ); ?></h2>
			<form id="post" method="post" name="post">
				<div id="papi-hidden-editor" class="hide-if-js">
					<?php wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' ); ?>
					<?php wp_editor( '', 'papiHiddenEditor' ); ?>
				</div>
				<div id="poststuff">
					<div id="post-body">
						<?php
						do_meta_boxes(
							$this->post_type[0],
							'normal',
							null
						);
						?>
						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}
