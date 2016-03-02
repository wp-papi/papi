<?php

/**
 * Admin class that handles taxonomy modifications.
 */
final class Papi_Admin_Taxonomy {

	/**
	 * All taxonomy types.
	 *
	 * @var array
	 */
	protected $taxonomy_types;

	/**
	 * The construct.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Add form fields to edit tags page.
	 */
	public function add_form_fields() {
		$html_name       = esc_attr( papi_get_page_type_key() );
		$taxonomy        = papi_get_qs( 'taxonomy' );
		$taxonomy_object = get_taxonomy( $taxonomy );

		// Get only the taxonomy types that has the taxonomy.
		$taxonomy_types  = array_filter( $this->taxonomy_types, function ( $taxonomy_type ) use( $taxonomy ) {
			return in_array( $taxonomy, $taxonomy_type->taxonomy ) && $taxonomy_type->display( $taxonomy );
		} );
		$taxonomy_types = array_values( $taxonomy_types );

		// Do not display empty select if no taxonomy types.
		if ( empty( $taxonomy_types ) ) {
			return;
		}

		// Render a dropdown if more than one taxonomy types
		// exists on the taxonomy.
		if ( count( $taxonomy_types ) > 1 ):
		?>
		<div class="form-field">
			<label for="<?php echo $html_name; ?>">
				<?php echo sprintf( __( '%s type', 'papi' ), $taxonomy_object->labels->singular_name ); ?>
			</label>
			<select name="<?php echo $html_name; ?>" id="<?php echo $html_name; ?>">
				<?php foreach ( $taxonomy_types as $taxonomy_type ): ?>
					<option value="<?php echo esc_attr( $taxonomy_type->get_id() ); ?>">
						<?php echo $taxonomy_type->name; ?>
					</option>
				<?php endforeach; ?>
			</select>
			<!-- additional info? -->
		</div>
		<?php
		else:
			papi_render_html_tag( 'input', [
				'data-papi-page-type-key' => true,
				'name'                    => esc_attr( papi_get_page_type_key() ),
				'type'                    => 'hidden',
				'value'                   => esc_attr( $taxonomy_types[0]->get_id() )
			] );
		endif;
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		add_action( 'admin_init', [$this, 'setup_taxonomies_hooks'] );
	}

	/**
	 * Setup hooks for all taxonomies.
	 */
	public function setup_taxonomies_hooks() {
		$this->taxonomy_types = papi_get_all_entry_types( [
			'types'	=> 'taxonomy'
		] );

		$taxonomies = array_reduce( $this->taxonomy_types, function ( $taxonomies, $taxonomy_type ) {
			return array_merge( is_array( $taxonomies ) ? $taxonomies : [], $taxonomy_type->taxonomy );
		} );
		$taxonomies = array_unique( $taxonomies );

		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', [$this, 'add_form_fields'] );
		}
	}
}

if ( is_admin() ) {
	new Papi_Admin_Taxonomy;
}
