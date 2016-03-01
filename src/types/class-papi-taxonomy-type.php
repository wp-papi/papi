<?php

/**
 * Papi type that handle taxonomies.
 */
class Papi_Taxonomy_Type extends Papi_Entry_Type {

	/**
	 * The taxonomy.
	 *
	 * @var string
	 */
	public $taxonomy = '';

	/**
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'taxonomy';

	/**
	 * Should the Taxonomy Type be displayed in WordPress admin or not?
	 *
	 * @param  string $taxonomy
	 *
	 * @return bool
	 */
	public function display( $taxonomy ) {
		return true;
	}

	/**
	 * Setup all meta boxes.
	 */
	public function setup() {
		if ( ! method_exists( $this, 'register' ) ) {
			return;
		}

		$boxes = $this->get_boxes();

		foreach ( $boxes as $box ) {
			new Papi_Admin_Meta_Box( $box );
		}
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		if ( empty( $this->taxonomy ) ) {
			return;
		}

		foreach ( $this->taxonomy as $taxonomy ) {
			add_action( $taxonomy . '_term_edit_form_top', [$this, 'edit_form_top'] );
			add_action( $taxonomy . '_edit_form', [$this, 'edit_form'] );
		}
	}

	/**
	 * Render edit form top.
	 * Requires 4.5.
	 */
	public function edit_form_top() {
		?>
		<h2 class="hndle"><span>Tag</span></h2>
		<?php
	}

	/**
	 * Render term edit form.
	 */
	public function edit_form() {
		?>
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
			</div>
		</div>
		<?php
	}

	/**
	 * Setup meta data.
	 */
	protected function setup_meta_data() {
		parent::setup_meta_data();
		$this->taxonomy = papi_to_array( $this->taxonomy );
	}
}
