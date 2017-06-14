<?php

/**
 * Admin class that handle table columns.
 */
final class Papi_Admin_Columns {

	/**
	 * Current post type.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Current taxonomy.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Get meta type value.
	 *
	 * @return string
	 */
	protected function get_meta_type_value() {
		return empty( $this->taxonomy ) ? $this->post_type : $this->taxonomy;
	}

	/**
	 * Add custom table header to page or taxonomy type.
	 *
	 * @param  array $defaults
	 *
	 * @return array
	 */
	public function manage_page_type_posts_columns( array $defaults = [] ) {
		if ( ! in_array( $this->post_type, papi_get_post_types(), true ) && ! in_array( $this->taxonomy, papi_get_taxonomies(), true ) ) {
			return $defaults;
		}

		/**
		 * Hide column for post or taxonomy type. Default is false.
		 *
		 * @param string $post_type
		 */
		if ( apply_filters( 'papi/settings/column_hide_' . $this->get_meta_type_value(), false ) ) {
			return $defaults;
		}

		/**
		 * Change column title for entry type column.
		 *
		 * @param string $post_type
		 */
		$defaults['entry_type'] = apply_filters(
			'papi/settings/column_title_' . $this->get_meta_type_value(),
			esc_html__( 'Type', 'papi' )
		);

		return $defaults;
	}

	/**
	 * Add custom table column to page or taxonomy type.
	 *
	 * @param string $column_name
	 * @param int    $post_id
	 * @param int    $term_id
	 */
	public function manage_page_type_posts_custom_column( $column_name, $post_id, $term_id = null ) {
		if ( ! in_array( $this->post_type, papi_get_post_types(), true ) && ! in_array( $this->taxonomy, papi_get_taxonomies(), true ) ) {
			return;
		}

		/**
		 * Hide column for post type. Default is false.
		 *
		 * @param string $post_type
		 */
		if ( apply_filters( 'papi/settings/column_hide_' . $this->get_meta_type_value(), false ) ) {
			return;
		}

		// Column name most be `entry_type`. On taxomy the column name is `post_id` variable.
		if ( $column_name !== 'entry_type' && $post_id !== 'entry_type' ) {
			return;
		}

		// Get the entry type for the post or term.
		$entry_type = papi_get_entry_type_by_meta_id(
			is_numeric( $post_id ) ? $post_id : $term_id,
			papi_get_meta_type()
		);

		$post = ! empty( $this->post_type ) && empty( $this->taxonomy );
		$type = $post ? 'page' : 'taxonomy';
		$arg  = $post ? papi_get_post_type() : papi_get_taxonomy();
		$show = call_user_func_array( "papi_filter_settings_show_standard_{$type}_type", [$arg] );

		if ( ! is_null( $entry_type ) && ( $show === false || get_metadata( papi_get_meta_type( $type ), ( is_numeric( $post_id ) ? $post_id : $term_id ), papi_get_page_type_key(), true ) === $entry_type->get_id() ) ) {
			echo esc_html( $entry_type->name );
		} else {
			echo esc_html( call_user_func_array( "papi_filter_settings_standard_{$type}_type_name", [$arg] ) );
		}
	}

	/**
	 * Add entry type as a sortable column.
	 *
	 * @param  array $columns
	 *
	 * @return array
	 */
	public function manage_page_type_sortable_columns( $columns ) {
		if ( ! in_array( $this->post_type, papi_get_post_types(), true ) && ! in_array( $this->taxonomy, papi_get_taxonomies(), true ) ) {
			return;
		}

		$columns['entry_type'] = 'entry_type';

		return $columns;
	}

	/**
	 * Filter posts on load if `page_type` query string is set.
	 *
	 * @param  WP_Query $query
	 *
	 * @return WP_Query
	 */
	public function pre_get_posts( WP_Query $query ) {
		global $pagenow;

		if ( $pagenow === 'edit.php' && ! is_null( papi_get_qs( 'page_type' ) ) ) {
			if ( papi_get_qs( 'page_type' ) === 'papi-standard-page' ) {
				$query->set( 'meta_query', [
					[
						'key'     => papi_get_page_type_key(),
						'compare' => 'NOT EXISTS'
					]
				] );
			} else {
				$query->set( 'meta_key', papi_get_page_type_key() );
				$query->set( 'meta_value', papi_get_qs( 'page_type' ) );
			}
		}

		// Order query result by entry type.
		if ( $query->get( 'orderby' ) === 'entry_type' ) {
			$query->set( 'meta_key', papi_get_page_type_key() );
			$query->set( 'orderby', 'meta_value' );
		}

		return $query;
	}

	/**
	 * Filter page types in post type list.
	 */
	public function restrict_page_types() {
		$post_types = papi_get_post_types();

		if ( in_array( $this->post_type, $post_types, true ) ) {
			$page_types = papi_get_all_page_types( $this->post_type );
			$page_types = array_map( function ( $page_type ) {
				return [
					'name'  => $page_type->name,
					'value' => $page_type->get_id()
				];
			}, $page_types );

			// Add the standard page that isn't a real page type.
			if ( papi_filter_settings_show_standard_page_type_in_filter( $this->post_type ) ) {
				$page_types[] = [
					'name'  => papi_filter_settings_standard_page_type_name( $this->post_type ),
					'value' => 'papi-standard-page'
				];
			}

			usort( $page_types, function ( $a, $b ) {
				return strcmp(
					strtolower( $a['name'] ),
					strtolower( $b['name'] )
				);
			} );
			?>
			<select name="page_type" class="postform">
				<option value="0" selected><?php esc_html_e( 'All types', 'papi' ); ?></option>
				<?php
				foreach ( $page_types as $page_type ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $page_type['value'] ),
						papi_get_qs( 'page_type' ) === $page_type['value'] ? ' selected' : '',
						esc_html( $page_type['name'] )
					);
				}
				?>
			</select>
			<?php
		}
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		// Setup post type actions.
		if ( ! empty( $this->post_type ) && empty( $this->taxonomy ) ) {
			add_filter( 'pre_get_posts', [$this, 'pre_get_posts'] );
			add_action( 'restrict_manage_posts', [$this, 'restrict_page_types'] );
			add_action( sprintf( 'manage_%s_posts_custom_column', $this->post_type ), [$this, 'manage_page_type_posts_custom_column'], 10, 2 );
		}

		// Setup taxonomy actions.
		if ( ! empty( $this->taxonomy ) ) {
			add_action( sprintf( 'manage_%s_custom_column', $this->taxonomy ), [$this, 'manage_page_type_posts_custom_column'], 10, 3 );
		}
	}

	/**
	 * Setup filters.
	 */
	protected function setup_filters() {
		// Setup post type actions.
		if ( ! empty( $this->post_type ) && empty( $this->taxonomy ) ) {
			add_filter( sprintf( 'manage_%s_posts_columns', $this->post_type ), [$this, 'manage_page_type_posts_columns'] );
			add_filter( sprintf( 'manage_edit-%s_sortable_columns', $this->post_type ), [$this, 'manage_page_type_sortable_columns'] );
		}

		// Setup taxonomy actions.
		if ( ! empty( $this->taxonomy ) ) {
			add_filter( sprintf( 'manage_edit-%s_columns', $this->taxonomy ), [$this, 'manage_page_type_posts_columns'] );
			add_filter( sprintf( 'manage_edit-%s_sortable_columns', $this->taxonomy ), [$this, 'manage_page_type_sortable_columns'] );
		}
	}

	/**
	 * Setup globals.
	 */
	protected function setup_globals() {
		$this->post_type = papi_get_post_type();
		$this->taxonomy  = papi_get_taxonomy();
	}
}

if ( papi_is_admin() ) {
	new Papi_Admin_Columns;
}
