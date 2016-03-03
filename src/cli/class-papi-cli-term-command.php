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
}
