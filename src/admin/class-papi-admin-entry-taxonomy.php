<?php

/**
 * Admin class that handles taxonomy modifications.
 */
class Papi_Admin_Entry_Taxonomy extends Papi_Admin_Entry {

	/**
	 * All taxonomy types.
	 *
	 * @var array
	 */
	protected $taxonomy_types = [];

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
		$taxonomy_types = array_filter( $this->taxonomy_types, function ( $taxonomy_type ) use ( $taxonomy ) {
			return in_array( $taxonomy, $taxonomy_type->taxonomy, true ) && $taxonomy_type->display( $taxonomy );
		} );
		$taxonomy_types = array_values( $taxonomy_types );

		// Do not display empty select if no taxonomy types.
		if ( empty( $taxonomy_types ) ) {
			return;
		}

		// Prepare taxonomy types with standard taxonomy type.
		$taxonomy_types = $this->prepare_taxonomy_types( $taxonomy_types );

		// Render a dropdown if more than one taxonomy types
		// exists on the taxonomy.
		if ( count( $taxonomy_types ) > 1 ):
			?>
			<div class="form-field">
				<label for="<?php echo esc_attr( $html_name ); ?>">
					<?php echo esc_html( sprintf( __( '%s type', 'papi' ), $taxonomy_object->labels->singular_name ) ); ?>
				</label>
				<select name="<?php echo esc_attr( $html_name ); ?>" id="<?php echo esc_attr( $html_name ); ?>" data-papi-page-type-key="true">
					<?php
					foreach ( $taxonomy_types as $taxonomy_type ) {
						papi_render_html_tag( 'option', [
							'data-redirect' => $taxonomy_type->redirect_after_create,
							'value'         => esc_attr( $taxonomy_type->get_id() ),
							esc_html( $taxonomy_type->name )
						] );
					}
					?>
				</select>
			</div>
			<?php
		else:
			papi_render_html_tag( 'input', [
				'data-redirect'           => $taxonomy_types[0]->redirect_after_create,
				'data-papi-page-type-key' => true,
				'name'                    => esc_attr( $html_name ),
				'type'                    => 'hidden',
				'value'                   => esc_attr( $taxonomy_types[0]->get_id() )
			] );
		endif;
	}

	/**
	 * Prepare taxonomy types, add standard taxonomy if it should be added.
	 *
	 * @param  array $taxonomy_types
	 *
	 * @return array
	 */
	protected function prepare_taxonomy_types( array $taxonomy_types ) {
		$taxonomy = papi_get_qs( 'taxonomy' );

		if ( papi_filter_settings_show_standard_taxonomy_type( $taxonomy ) ) {
			$id                      = sprintf( 'papi-standard-%s-type', $taxonomy );
			$taxonomy_type           = new Papi_Taxonomy_Type( $id );
			$taxonomy_type->id       = $id;
			$taxonomy_type->name     = papi_filter_settings_standard_taxonomy_type_name( $taxonomy );
			$taxonomy_type->taxonomy = [$taxonomy];
			$taxonomy_types[]        = $taxonomy_type;
		}

		usort( $taxonomy_types, function ( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );

		return papi_sort_order( array_reverse( $taxonomy_types ) );
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_init', [$this, 'setup_taxonomies_hooks'] );
	}

	/**
	 * Setup hooks for all taxonomies.
	 */
	public function setup_taxonomies_hooks() {
		$this->taxonomy_types = papi_get_all_entry_types( [
			'types' => 'taxonomy'
		] );

		$taxonomies = array_reduce( $this->taxonomy_types, function ( $taxonomies, $taxonomy_type ) {
			return array_merge( $taxonomies, $taxonomy_type->taxonomy );
		}, [] );
		$taxonomies = array_unique( $taxonomies );

		foreach ( $taxonomies as $taxonomy ) {
			if ( is_string( $taxonomy ) && taxonomy_exists( $taxonomy ) ) {
				add_action( $taxonomy . '_add_form_fields', [$this, 'add_form_fields'] );
			}
		}
	}
}

if ( papi_is_admin() ) {
	Papi_Admin_Entry_Taxonomy::instance();
}
