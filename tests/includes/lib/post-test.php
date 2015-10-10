<?php

/**
 * Unit tests covering post functions.
 *
 * @package Papi
 */
class Papi_Lib_Post_Test extends WP_UnitTestCase {

	public function test_papi_get_post_id() {
		global $post;

		$post_id = $this->factory->post->create();

		$post = get_post( $post_id );
		$this->assertSame( 1, papi_get_post_id( 1 ) );
		$this->assertSame( $post_id, papi_get_post_id() );
		$this->assertSame( $post_id, papi_get_post_id( null ) );

		$this->assertSame( $post_id, papi_get_post_id( $post ) );
		$this->assertSame( 1, papi_get_post_id( '1' ) );

		$post = null;

		$_GET = ['post' => $post_id];
		$this->assertSame( $post_id, papi_get_post_id() );
		unset( $_GET );

		$_GET = ['post' => [1, 2, 3]];
		$this->assertSame( 0, papi_get_post_id() );
		unset( $_GET );

		$_GET = ['page_id' => $post_id];
		$this->assertSame( $post_id, papi_get_post_id() );
		unset( $_GET );

		$_POST = [
			'action' => 'query-attachments',
			'query'  => [
				'item' => $post_id
			]
		];
		$this->assertSame( $post_id, papi_get_post_id() );
		unset( $_POST );

		$_GET  = [
			'post' => $post_id
		];
		$_POST = [
			'action' => 'query-attachments'
		];
		$this->assertSame( $post_id, papi_get_post_id() );
		unset( $_POST );
	}

	public function test_papi_get_parent_post_id() {
		$this->assertEmpty( papi_get_parent_post_id() );
		$_GET['post_parent'] = 7;
		$this->assertSame( 7, papi_get_parent_post_id() );
		unset( $_GET );
	}

	public function test_papi_get_post_type() {
		global $post;

		$this->assertEmpty( papi_get_post_type() );

		$_GET = ['post_type' => 'post'];
		$this->assertSame( 'post', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,books'];
		$this->assertSame( 'books', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,dash-post'];
		$this->assertSame( 'dash-post', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,und_post'];
		$this->assertSame( 'und_post', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,3414'];
		$this->assertSame( '3414', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,dash13'];
		$this->assertSame( 'dash13', papi_get_post_type() );

		$_GET = ['page' => ''];
		$this->assertEmpty( papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,'];
		$this->assertEmpty( papi_get_post_type() );
		unset( $_GET );

		$_POST = ['post_type' => 'page'];
		$this->assertSame( 'page', papi_get_post_type() );
		unset( $_POST );

		$_SERVER['REQUEST_URI'] = 'wordpress/wp-admin/post-new.php';
		$this->assertSame( 'post', papi_get_post_type() );
		$_SERVER['REQUEST_URI'] = '';

		$post_id = $this->factory->post->create();
		$post = get_post( $post_id );
		$this->assertSame( 'post', papi_get_post_type() );
	}

	public function test_papi_get_post_type_label() {
		$this->assertEmpty( papi_get_post_type_label( 'fake', 'name', '' ) );
		$this->assertSame( 'Posts', papi_get_post_type_label( 'post', 'name' ) );
	}
}
