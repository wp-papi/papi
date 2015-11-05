<?php

/**
 * Papi type that handle option, option data
 * and rendering. All option types should extend this
 * class.
 */
class Papi_Option_Type extends Papi_Data_Type {

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
						do_meta_boxes(
							get_class( $this ),
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
		$this->boxes = $this->get_boxes();

		foreach ( $this->boxes as $box ) {
			$box[0]['_meta_box_id'] = get_class( $this );
			new Papi_Admin_Meta_Box( $box[0], $box[1] );
		}
	}
}
