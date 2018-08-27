<?php

/**
 * Manage terms.
 */
class Papi_CLI_Term_Command extends Papi_CLI_Command {

	/**
	 * Get default fields for formatter.
	 *
	 * @return array
	 */
	protected function get_default_format_fields() {
		return ['slug', 'type', 'has value', 'box'];
	}

	/**
	 * Get fields that exists on a term.
	 *
	 * ## OPTIONS
	 *
	 * <term>
	 * : Term ID
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole term fields, returns the value of a single fields.
	 *
	 * [--fields=<fields>]
	 * : Get a specific subset of the term's fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv. Default: table.
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields are available for get command:
	 *
	 * * slug
	 * * type
	 * * exists
	 *
	 * ## EXAMPLES
	 *
	 *     wp papi term get 123 --format=json
	 *
	 *     wp papi term get 123 --field=slug
	 *
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function get( $args, $assoc_args ) {
		try {
			// Set query string that we need.
			$_GET['meta_type'] = 'term';

			// Get the taxonomy type that the term has.
			$entry_type = papi_get_entry_type_by_meta_id( $args[0] );

			if ( empty( $entry_type ) || $entry_type instanceof Papi_Taxonomy_Type === false ) {
				WP_CLI::error( 'No taxonomy type exists on the term' );
			}

			$properties = [];

			foreach ( $entry_type->get_boxes() as $box ) {
				foreach ( $box->properties as $property ) {
					$properties[] = [
						'slug'      => $property->get_slug( true ),
						'type'      => $property->type,
						'has value' => $property->get_value() !== null ? 'true' : 'false',
						'box'       => $box->title
					];
				}
			}

			// Render types as a table.
			$formatter = $this->get_formatter( $assoc_args );
			$formatter->display_items( $properties );
		} catch ( WC_CLI_Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

	/**
	 * Rename meta key for taxonomy type.
	 *
	 * ## OPTIONS
	 *
	 * <taxonomy_type>
	 * : Taxonomy type id
	 *
	 * <old_key>
	 * : Old meta key
	 *
	 * <new_key>
	 * : New meta key
	 *
	 * ## EXAMPLES
	 *
	 *     wp papi term rename about-page-type name title
	 *
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function rename( $args, $assoc_args ) {
		$type    = $args[0];
		$old_key = $args[1];
		$new_key = $args[2];

		$terms = ( new Papi_Query( [
			'entry_type' => $type,
			'fields'     => 'ids'
		] ) )->get_result();

		if ( empty( $terms ) ) {
			WP_CLI::error( 'No terms found' );
		}

		foreach ( $terms as $term ) {
			$meta = get_term_meta( $term, $old_key, true );

			if ( papi_is_empty( $meta ) ) {
				continue;
			}

			if ( delete_term_meta( $term, $old_key ) === false ) {
				WP_CLI::error( 'Could not delete term meta with key: ' . $old_key );

			}

			if ( update_term_meta( $term, $new_key, $meta ) === false ) {
				WP_CLI::error( 'Could not update term meta with key: ' . $new_key );
			}
		}

		WP_CLI::success( 'Done' );
	}
}
