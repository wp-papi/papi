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
		return ['name', 'id', 'post_type', 'template', 'number_of_pages', 'type'];
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
	public function list_( $_, $assoc_args ) {
		// Get all page types.
		$types = papi_get_all_page_types( true );

		// Get all fake types.
		$types = array_merge( $types, papi_get_all_page_types( false, null, true ) );

		// Create type item with the fields that
		// will be displayed.
		$types = array_map( function( $type ) {
			$fake_post_type = preg_match( '/\_papi/', $type->get_post_type() );

			return [
				'id'              => $type->get_id(),
				'name'            => $type->name,
				'post_type'       => $fake_post_type ? 'n/a' : implode( ', ', $type->post_type ),
				'template'        => $fake_post_type ? 'n/a' : $type->template,
				'type'            => $type->get_type(),
				'number_of_pages' => $fake_post_type ? 'n/a' : papi_get_number_of_pages( $type->get_id() )
			];
		}, $types );

		// Render types as a table.
		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_items( $types );
	}

}
