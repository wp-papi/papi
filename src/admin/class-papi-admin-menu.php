<?php

/**
 * Admin class that handles admin menus.
 */
final class Papi_Admin_Menu {

	/**
	 * The construct.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Fill labels on admin bar.
	 */
	public function admin_bar_menu() {
		if ( $page_type = $this->get_page_type() ) {
			$this->override_labels( $page_type );
		}
	}

	/**
	 * Get current page type.
	 *
	 * @return Papi_Page_Type
	 */
	private function get_page_type() {
		if ( $page_type = papi_get_page_type_by_id( papi_get_page_type_id() ) ) {
			return $page_type;
		}
	}

	/**
	 * Override labels with labels from the page type.
	 *
	 * @param Papi_Page_Type $page_type
	 */
	private function override_labels( Papi_Page_Type $page_type ) {
		global $wp_post_types;

		$post_type = papi_get_post_type();

		if ( empty( $post_type ) || ! isset( $wp_post_types[$post_type] ) ) {
			return;
		}

		foreach ( $page_type->get_labels() as $key => $value ) {
			if ( ! isset( $wp_post_types[$post_type]->labels->$key ) || empty( $value ) ) {
				continue;
			}

			$wp_post_types[$post_type]->labels->$key = $value;
		}
	}

	/**
	 * Page items menu.
	 *
	 * This function will register all page types
	 * that has a fake post type. Like option types.
	 */
	public function page_items_menu() {
		$page_types = papi_get_all_page_types( false, null, true );

		foreach ( $page_types as $page_type ) {
			if ( empty( $page_type->menu ) || empty( $page_type->name ) ) {
				continue;
			}

			$slug = 'papi/' . $page_type->get_id();

			add_submenu_page(
				$page_type->menu,
				$page_type->name,
				$page_type->name,
				$page_type->capability,
				$slug,
				[$page_type, 'render']
			);
		}
	}

	/**
	 * Setup menu items for real post types.
	 */
	public function post_types_menu() {
		global $submenu;

		$post_types = papi_get_post_types();

		foreach ( $post_types as $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}

			if ( $post_type === 'post' ) {
				$edit_url = 'edit.php';
			} else {
				$edit_url = 'edit.php?post_type=' . $post_type;
			}

			if ( ! isset( $submenu[$edit_url] ) || ! isset( $submenu[$edit_url][10] ) || ! isset( $submenu[$edit_url][10][2] ) ) {
				$post_type_object = get_post_type_object( $post_type );
				if ( ! $post_type_object->show_in_menu ) {
					$submenu[$edit_url] = [
						10 => [
							__( 'Add New', 'papi' ),
							'edit_posts',
							'post-new.php'
						]
					];
				} else {
					continue;
				}
			}

			$only_page_type = papi_filter_settings_only_page_type( $post_type );
			$page_types     = papi_get_all_page_types( false, $post_type );
			$show_standard  = false;

			// Don't change menu item when no page types is found.
			// @codeCoverageIgnoreStart
			if ( empty( $page_types ) ) {
				continue;
			}
			// @codeCoverageIgnoreEnd

			if ( count( $page_types ) === 1 && empty( $only_page_type ) ) {
				$show_standard  = $page_types[0]->standard_type;
				$show_standard  = $show_standard ?
					papi_filter_settings_show_standard_page_type( $post_type ) :
					$show_standard;

				$only_page_type = $show_standard ? '' : $page_types[0]->get_id();
			}

			if ( ! empty( $only_page_type ) && ! $show_standard ) {
				$submenu[$edit_url][10][2] = papi_get_page_new_url(
					$only_page_type,
					false,
					$post_type,
					[
						'action',
						'message',
						'page_type',
						'post',
						'post_type'
					]
				);
			} else {
				$page  = 'papi-add-new-page,' . $post_type;
				$start = strpos( $edit_url, 'post_type' ) === false ? '?' : '&';

				$submenu[$edit_url][10][2] = sprintf(
					'%s%spage=%s',
					$edit_url,
					$start,
					$page
				);

				// Add menu item.
				add_menu_page(
					__( 'Add New', 'papi' ),
					__( 'Add New', 'papi' ),
					'read',
					$page,
					[$this, 'render_view']
				);

				// Remove the menu item so it's hidden.
				remove_menu_page( $page );
			}
		}
	}

	/**
	 * Menu callback that loads right view depending on what the `page` query string says.
	 */
	public function render_view() {
		if ( strpos( papi_get_qs( 'page' ), 'papi' ) !== false ) {
			$page = str_replace( 'papi-', '', papi_get_qs( 'page' ) );
			$res = preg_replace( '/\,.*/', '', $page );

			if ( is_string( $res ) ) {
				$page_view = $res;
			}
		}

		if ( ! isset( $page_view ) ) {
			$page_view = null;
		}

		if ( ! is_null( $page_view ) ) {
			$view = new Papi_Admin_View;
			$view->render( $page_view );
		} else {
			echo '<h2>Papi - 404</h2>';
		}
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		if ( is_admin() ) {
			add_action( 'admin_init', [$this, 'admin_bar_menu'] );
			add_action( 'admin_menu', [$this, 'page_items_menu'] );
			add_action( 'admin_menu', [$this, 'post_types_menu'] );
		} else {
			add_action( 'admin_bar_menu', [$this, 'admin_bar_menu'] );
		}
	}
}

new Papi_Admin_Menu;
