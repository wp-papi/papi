<?php

/**
 * Manage types.
 */
class Papi_CLI_Type_Command extends Papi_CLI_Command {

	/**
	 * Get default fields for formatter.
	 *
	 * @return array
	 */
	protected function get_default_format_fields() {
		return ['name', 'id', 'meta type value', 'template', 'db count', 'type'];
	}

	/**
	 * Get meta type value.
	 *
	 * @param  Papi_Entry_Type $entry_type
	 *
	 * @return string
	 */
	protected function get_meta_type_value( $entry_type ) {
		if ( in_array( $entry_type->get_type(), ['attachment'], true ) ) {
			return $entry_type->get_type();
		}

		switch ( papi_get_meta_type( $entry_type->get_type() ) ) {
			case 'post':
				return implode( ', ', $entry_type->post_type );
			case 'term':
				return implode( ', ', $entry_type->taxonomy );
			default:
				return 'n/a';
		}
	}

	/**
	 * List Papi types.
	 *
	 * ## Options
	 *
	 * [--<field>=<value>]
	 * : Filter types based on type property.
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each type.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific type fields.
	 *
	 * [--format=<format>]
	 * : Acceptec values: table, csv, json, count, ids. Default: table.
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each type:
	 *
	 * * name
	 * * id
	 * * post_type
	 * * template
	 * * number_of_pages
	 * * type
	 *
	 * Not all fields exists on a Papi type so some fields will have `n/a`
	 * as value when no value can be displayed.
	 *
	 * ## EXAMPLES
	 *
	 *     wp papi type list
	 *
	 * @subcommand list
	 */
	public function list_( $args, $assoc_args ) {
		// Get all entry types.
		$entry_types = papi_get_all_entry_types();

		if ( empty( $entry_types ) ) {
			WP_CLI::error( 'No Papi types exists.' );
		}

		// Create type item with the fields that
		// will be displayed.
		$entry_types = array_map( function( $entry_type ) {
			return [
				'id'              => $entry_type->get_id(),
				'name'            => $entry_type->name,
				'meta type value' => $this->get_meta_type_value( $entry_type ),
				'template'        => empty( $entry_type->template ) ? 'n/a' : $entry_type->template,
				'type'            => $entry_type->get_type(),
				'db count'        => $entry_type->type === 'option' ? 'n/a' : papi_get_entry_type_count( $entry_type )
			];
		}, $entry_types );

		// Render types as a table.
		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_items( $entry_types );
	}
}
