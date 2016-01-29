<?php

/**
 * Manage posts.
 */
class Papi_CLI_Post_Command extends Papi_CLI_Command {

	/**
	 * Get default fields for formatter.
	 *
	 * @return array
	 */
	protected function get_default_format_fields() {
		return ['slug', 'type', 'has value'];
	}

	/**
	 * Get fields that exists on a post.
	 *
	 * ## OPTIONS
	 *
	 * <post>
	 * : Post ID
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole post fields, returns the value of a single fields.
	 *
	 * [--fields=<fields>]
	 * : Get a specific subset of the post's fields.
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
	 *     wp papi post get 123 --format=json
	 *
	 *     wp papi post get 123 --field=slug
	 *
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function get( $args, $assoc_args ) {
		try {
			// Set post query string to post id.
			$_GET['post'] = $args[0];

			// Get the page type that the post has.
			$page_type = papi_get_page_type_by_post_id( $args[0] );

			if ( empty( $page_type ) ) {
				WP_CLI::error( 'No page type exists on the post' );
			}

			$properties = [];

			foreach ( $page_type->get_boxes() as $box ) {
				foreach ( $box->properties as $property ) {
					$properties[] = [
						'slug'      => $property->get_slug( true ),
						'type'      => $property->type,
						'has value' => $property->get_value() !== null ? 'true' : 'false',
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
