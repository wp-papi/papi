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
		return ['slug', 'type', 'has value', 'box'];
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
			// Set query string that we need.
			$_GET['post'] = $args[0];

			// Get the page type that the post has.
			$entry_type = papi_get_entry_type_by_meta_id( $args[0] );

			if ( empty( $entry_type ) || $entry_type instanceof Papi_Page_Type === false ) {
				WP_CLI::error( 'No page type exists on the post' );
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
	 * Rename meta key for page type.
	 *
	 * ## OPTIONS
	 *
	 * <page_type>
	 * : Page type id
	 *
	 * <old_key>
	 * : Old meta key
	 *
	 * <new_key>
	 * : New meta key
	 *
	 * ## EXAMPLES
	 *
	 *     wp papi post rename about-page-type name title
	 *
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function rename( $args, $assoc_args ) {
		$type    = $args[0];
		$old_key = $args[1];
		$new_key = $args[2];

		$posts = ( new Papi_Query( [
			'entry_type' => $type,
			'fields'     => 'ids'
		] ) )->get_result();

		if ( empty( $posts ) ) {
			WP_CLI::error( 'No posts found' );
		}

		foreach ( $posts as $post ) {
			$meta = get_post_meta( $post, $old_key, true );

			if ( papi_is_empty( $meta ) ) {
				continue;
			}

			if ( delete_post_meta( $post, $old_key ) === false ) {
				WP_CLI::error( 'Could not delete post meta with key: ' . $old_key );

			}

			if ( update_post_meta( $post, $new_key, $meta ) === false ) {
				WP_CLI::error( 'Could not update post meta with key: ' . $new_key );
			}
		}

		WP_CLI::success( 'Done' );
	}
}
