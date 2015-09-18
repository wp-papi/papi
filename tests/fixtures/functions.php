<?php

/**
 * Say Hello!
 */
function say_hello_stub() {
	echo 'Hello';
}

/**
 * Say Hello!
 *
 * @param string $name
 */
function say_hello_name_stub( $name ) {
	echo "Hello $name";
}

/**
 * Register a book post type.
 */
function papi_test_register_book_post_type() {
    global $wp_post_types;

	$labels = [
        'name'               => _x( 'Books', 'post type general name', 'papi-tests' ),
        'singular_name'      => _x( 'Book', 'post type singular name', 'papi-tests' ),
        'menu_name'          => _x( 'Books', 'admin menu', 'papi-tests' ),
        'name_admin_bar'     => _x( 'Book', 'add new on admin bar', 'papi-tests' ),
        'add_new'            => _x( 'Add New', 'book', 'papi-tests' ),
        'add_new_item'       => __( 'Add New Book', 'papi-tests' ),
        'new_item'           => __( 'New Book', 'papi-tests' ),
        'edit_item'          => __( 'Edit Book', 'papi-tests' ),
        'view_item'          => __( 'View Book', 'papi-tests' ),
        'all_items'          => __( 'All Books', 'papi-tests' ),
        'search_items'       => __( 'Search Books', 'papi-tests' ),
        'parent_item_colon'  => __( 'Parent Books:', 'papi-tests' ),
        'not_found'          => __( 'No books found.', 'papi-tests' ),
        'not_found_in_trash' => __( 'No books found in Trash.', 'papi-tests' )
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'book'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments']
    ];

    $out = register_post_type( 'book', $args );

	if ( ! isset( $wp_post_types['book'] ) ) {
		$wp_post_types['book'] = $out;
	}
}
