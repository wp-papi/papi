<?php

/**
 * @group admin
 */
class Papi_Admin_Meta_Box_Test extends WP_UnitTestCase {

	 public function test_add_property() {
 		$property = papi_property( [
 			'type'  => 'string',
 			'title' => 'Name'
 		] );
		$class = new Papi_Admin_Meta_Box(
			new Papi_Core_Box( [], [$property] )
		);
		$properties = function ( Papi_Admin_Meta_Box $box ) {
			return $box->box->properties;
		};
		$properties = Closure::bind( $properties, null, $class );
		$this->assertSame( [$property], $properties( $class ) );
	}

	public function test_after_title() {
		$box = new Papi_Core_Box( [
			'title' => 'Content'
		] );
		$class = new Papi_Admin_Meta_Box( $box );
		$this->assertFalse( has_action( 'edit_form_after_title', [$class, 'move_meta_box_after_title'] ) );

		$_GET['post_type'] = 'page';
		$box = new Papi_Core_Box( [
			'context' => 'after_title',
			'title'   => 'Content'
		] );
		$class = new Papi_Admin_Meta_Box( $box );
		$this->assertSame( 10, has_action( 'edit_form_after_title', [$class, 'move_meta_box_after_title'] ) );
		unset( $_GET['post_type'] );
	}

	public function test_admin_meta_box_construct() {
		$user_id = $this->factory->user->create( ['role' => 'read'] );
		$box = new Papi_Core_Box( [
			'title' => 'Content'
		] );
		$class = new Papi_Admin_Meta_Box( $box );

		$class->setup_meta_box();
		do_meta_boxes( '_papi_content', 'normal', null );
		$this->expectOutputRegex( '//' );
	}

	public function test_admin_meta_box_capabilities() {
		$box = new Papi_Core_Box( [
			'title' => 'Content',
			'capabilities' => ['admin']
		] );
		$class = new Papi_Admin_Meta_Box( $box );
		$this->assertTrue( ! isset( $class->box ) );
	}

	public function test_move_meta_box_after_title() {
		global $post, $wp_meta_boxes, $pagenow;
		$pagenow = 'post-new.php';
		$post_id = $this->factory->post->create( [ 'post_type' => 'page' ] );
		$post    = get_post( $post_id );
		$box     = new Papi_Core_Box( [
			'title' => 'Content'
		] );
		$class   = new Papi_Admin_Meta_Box( $box );
		set_current_screen( $pagenow );
		$class->move_meta_box_after_title();
		$this->assertFalse( isset( $wp_meta_boxes['page']['normal'] ) );
		$this->expectOutputRegex( '/.*\S.*/' );
		$GLOBALS['current_screen'] = null;
	}

	public function test_meta_box_css_classes() {
		$box = new Papi_Core_Box;
		$class = new Papi_Admin_Meta_Box( $box );
		$this->assertSame( ['papi-box'], $class->meta_box_css_classes( [] ) );
	}

	public function test_render_meta_box() {
		$box = new Papi_Core_Box( [
			'title' => 'Content'
		] );
		$class = new Papi_Admin_Meta_Box( $box );

		$this->assertNull( $class->render_meta_box( null, null ) );
		$this->assertNull( $class->render_meta_box( null, [] ) );

		$property = papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$class->render_meta_box( null, [
			'args' => [$property]
		] );

		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_required() {
		$property = papi_property( [
			'type'     => 'string',
			'title'    => 'Name',
			'required' => true
		] );

		$box = new Papi_Core_Box( [
			'title' => 'Content'
		], [$property] );
		$box->set_option( 'required', true );

		$class = new Papi_Admin_Meta_Box( $box );

		$class->setup_meta_box();
		$title = function ( Papi_Admin_Meta_Box $box ) {
			return $box->get_title();
		};
		$title = Closure::bind( $title, null, $class );
		$this->assertSame( sprintf(
			'Content <span class="papi-rq" data-property-name="%s" data-property-id="%s">%s</span>',
			$property->title,
			$property->slug,
			'(required field)'
		), $title( $class ) );
	}
}
