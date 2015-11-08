<?php

/**
 * Papi type that handle option, option data
 * and rendering. All option types should extend this
 * class.
 */
class Papi_Option_Type extends Papi_content_type {

	/**
	 * The method name to use instead of `meta`.
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
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'option';

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
						for ( $i = 0, $l = count( $this->boxes ); $i < $l; $i++ ) {
							do_meta_boxes(
								sprintf( '%s_%d', get_class( $this ), $i ),
								'normal',
								null
							);
						}
						?>
						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * This function will setup all meta boxes.
	 */
	public function setup() {
		if ( ! method_exists( $this, 'register' ) ) {
			return;
		}

		// 1. Run the register method.
		$this->register();

		// 2. Load all boxes.
		$boxes = $this->get_boxes();

		foreach ( $boxes as $index => $box ) {
			$box->id = sprintf( '%s_%d', get_class( $this ), $index );
			new Papi_Admin_Meta_Box( $box );
		}
	}
}
