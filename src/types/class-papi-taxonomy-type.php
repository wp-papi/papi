<?php

/**
 * Papi type that handle taxonomies.
 */
class Papi_Taxonomy_Type extends Papi_Entry_Type {

	/**
	 * Fill labels.
	 *
	 * @var bool
	 */
	public $fill_labels = false;

	/**
	 * Labels, the same labels that taxonomy type object uses.
	 *
	 * @var array
	 */
	public $labels = [];

	/**
	 * Redirect after create.
	 *
	 * @var bool
	 */
	public $redirect_after_create = false;

	/**
	 * The taxonomy.
	 *
	 * @var array
	 */
	public $taxonomy = [];

	/**
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'taxonomy';

	/**
	 * Determine if the taxonomy type is allowed by taxonomy type.
	 *
	 * @return bool
	 */
	public function allowed() {
		$args = func_get_args();

		if ( empty( $args ) ) {
			return parent::allowed();
		}

		return isset( $args[0] ) && in_array( $args[0], $this->taxonomy, true );
	}

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
	 * Get labels that should be changed
	 * when using `fill_labels` option.
	 *
	 * @return array
	 */
	public function get_labels() {
		if ( ! $this->fill_labels ) {
			return $this->labels;
		}

		return array_merge( $this->labels, [
			'add_new_item' => sprintf( '%s %s', __( 'Add New', 'papi' ), $this->name ),
			'edit_item'    => sprintf( '%s %s', __( 'Edit', 'papi' ), $this->name ),
			'view_item'    => sprintf( '%s %s', __( 'View', 'papi' ), $this->name )
		] );
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		foreach ( papi_to_array( $this->taxonomy ) as $taxonomy ) {
			if ( is_string( $taxonomy ) && taxonomy_exists( $taxonomy ) ) {
				add_action( $taxonomy . '_edit_form', [$this, 'edit_form'] );
			}
		}
	}

	/**
	 * Render term edit form.
	 */
	public function edit_form() {
		?>
		<div id="papi-hidden-editor" class="hide-if-js">
			<?php wp_editor( '', 'papiHiddenEditor' ); ?>
		</div>
		<div id="poststuff">
			<div id="post-body">
				<?php
				foreach ( $this->boxes as $box ) {
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
