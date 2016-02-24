<?php
/**
 * Papi type that handle taxonomies
 */
class Papi_Taxonomy_Type extends Papi_Entry_Type {

	/**
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'taxonomy';

	/**
	 * The constructor.
	 *
	 * Load a entry type by the file.
	 *
	 * @param string $file_path
	 */
	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
		$this->taxonomy = papi_to_array( $this->taxonomy );
	}

	/**
	 * This function will setup all meta boxes.
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
			add_action( $taxonomy . '_add_form_fields', [$this, 'add_term'] );
			add_action( $taxonomy . '_edit_form_fields', [$this, 'edit_term'] );
		}
	}

	/**
	 * Render the fields on the add term page
	 */
	public function add_term() {
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
					<?php
					echo papi_maybe_get_callable_value(
						'papi_render_property',
						$prop
					);
					?>
					<p><?php echo $prop->description; ?></p>
				</div>
			<?php
			}
		}
	}

	/**
	 * Render the fields on the edit term page
	 */
	public function edit_term() {
		foreach ( $this->get_boxes() as $box ) {
			?>
			<tr class="form-field">
				<th scope="row" valign="top" colspan="2" class="papi-taxonomy-title">
					<h2><?php echo $box->title; ?></h2>
				</th>
			</tr>
			<?php
			foreach ( $box->properties as $prop ) {
				// Raw output is required.
				$prop->raw = true;

				?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="<?php echo $prop->get_slug(); ?>"><?php echo $prop->title; ?></label>

					</th>
					<td>
						<?php
						echo papi_maybe_get_callable_value(
							'papi_render_property',
							$prop
						);
						?>
						<p class="description"><?php echo $prop->description; ?></p>
					</td>
				</tr>
			<?php
			}
		}
	}

	/**
	 * Check if the entry type is a singleton.
	 *
	 * @return bool
	 */
	public function singleton() {
		$return = true;

		foreach ( $this->taxonomy as $taxonomy ) {
			$key = sprintf( 'entry_type_id.taxonomy.%s', $taxonomy );
			if ( ! papi()->exists( $key ) ) {
				papi()->singleton( $key, $this->get_id() );

				$return = false;
			}
		}

		return $return;
	}
}
