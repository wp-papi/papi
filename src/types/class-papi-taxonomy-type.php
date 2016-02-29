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
	 * The type name. Used for WP CLI.
	 *
	 * @var string
	 */
	public $type = 'taxonomy';

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
			add_action( $taxonomy . '_add_form_fields', [$this, 'add_form'] );
			add_action( $taxonomy . '_term_edit_form_top', [$this, 'edit_form_top'] );
			add_action( $taxonomy . '_edit_form', [$this, 'edit_form'] );
		}
	}

	/**
	 * Render the fields on the add term page.
	 */
	public function add_form() {
		foreach ( $this->get_boxes() as $box ) {
			?>
			<h2><?php echo $box->title; ?></h2>
			<?php
			foreach ( $box->properties as $prop ) {
				// Raw output is required.
				$prop->raw = true;

				?>
				<div class="form-field">
					<label for="<?php echo $prop->get_slug(); ?>"><?php echo $prop->title; ?></label>
					<?php papi_render_property( $prop ); ?>
					<p><?php echo $prop->description; ?></p>
				</div>
			<?php
			}
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
						'post',
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

	/**
	 * Check if the entry type is a singleton.
	 *
	 * @return bool
	 */
	public function singleton() {
		$singleton = true;

		foreach ( $this->taxonomy as $taxonomy ) {
			$key = sprintf( 'entry_type_id.taxonomy.%s', $taxonomy );

			if ( ! papi()->exists( $key ) ) {
				papi()->singleton( $key, $this->get_id() );
				$singleton = false;
			}
		}

		return $singleton;
	}
}
