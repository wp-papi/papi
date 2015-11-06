<?php

/**
 * @group admin
 */
class Papi_Admin_Meta_Box_Test extends WP_UnitTestCase {

	public function test_add_property() {
		$class = new Papi_Admin_Meta_Box();
		$property = papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] );
		$class->add_property( $property );
		$properties = function ( Papi_Admin_Meta_Box $box ) {
			return $box->properties;
		};
		$properties = Closure::bind( $properties, null, $class );
		$this->assertSame( [$property], $properties( $class ) );
	}

	public function test_after_title() {
		$class = new Papi_Admin_Meta_Box( [
			'title'   => 'Content'
		] );
		$this->assertFalse( has_action( 'edit_form_after_title', [$class, 'move_meta_box_after_title'] ) );
		$class = new Papi_Admin_Meta_Box( [
			'context'    => 'after_title',
			'title'      => 'Content',
			'_post_type' => 'page'
		] );
		$this->assertSame( 10, has_action( 'edit_form_after_title', [$class, 'move_meta_box_after_title'] ) );
	}

	public function test_construct() {
		$class = new Papi_Admin_Meta_Box();

		$user_id = $this->factory->user->create( ['role' => 'read'] );
		$class = new Papi_Admin_Meta_Box( [
			'capabilities' => 'super',
			'title'        => 'Content'
		] );

		$class = new Papi_Admin_Meta_Box( [
			'title' => 'Content'
		] );

		$class->setup_meta_box();
		do_meta_boxes( '_papi_content', 'normal', null );
		$this->expectOutputRegex( '//' );

		$class = new Papi_Admin_Meta_Box( [
			'title' => 'Content'
		] );

		$class->setup_meta_box();
		do_meta_boxes( '_papi_content', 'normal', null );
		$this->expectOutputRegex( '//' );
	}

	public function test_move_meta_box_after_title() {
		global $post, $wp_meta_boxes, $pagenow;
		$pagenow = 'post-new.php';
		$post_id = $this->factory->post->create( [ 'post_type' => 'page' ] );
		$post    = get_post( $post_id );
		$class   = new Papi_Admin_Meta_Box( [
			'title' => 'Content'
		] );
		set_current_screen( $pagenow );
		$class->move_meta_box_after_title();
		$this->assertFalse( isset( $wp_meta_boxes['page']['normal'] ) );
		$this->expectOutputRegex( '/.*\S.*/' );
		$GLOBALS['current_screen'] = null;
	}

	public function test_meta_box_css_classes() {
		$class = new Papi_Admin_Meta_Box();
		$this->assertSame( ['papi-box'], $class->meta_box_css_classes( [] ) );
	}

	public function test_render_meta_box() {
		$class = new Papi_Admin_Meta_Box( [
			'title' => 'Content'
		] );

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

		$class = new Papi_Admin_Meta_Box( [
			'title'     => 'Content',
			'_required' => true
		], [$property] );

		$class->setup_meta_box();
		$title = function ( Papi_Admin_Meta_Box $box ) {
			return $box->options->title;
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
