<?php

/**
 * Papi type that handle option, option data
 * and rendering. All option types should extend this
 * class.
 */
class Papi_Option_Type extends Papi_Entry_Type {

	/**
	 * Capability.
	 *
	 * @var array
	 */
	public $capability = 'manage_options';

	/**
	 * The description of the option type.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * The menu to register the option type on.
	 *
	 * @var string
	 */
	public $menu = '';

	/**
	 * The type name. Used for WP CLI.
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
			<?php echo wpautop( papi_nl2br( $this->description ) ); ?>
			<form id="post" method="post" name="post">
				<div id="papi-hidden-editor" class="hide-if-js">
					<?php wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' ); ?>
					<?php wp_editor( '', 'papiHiddenEditor' ); ?>
				</div>
				<div id="poststuff">
					<div id="post-body">
						<?php
						foreach ( $this->boxes as $index => $box ) {
							do_meta_boxes(
								$box->id,
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
	 * Setup all meta boxes.
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
			new Papi_Admin_Meta_Box( $box );
		}
	}
}
