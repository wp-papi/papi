<?php

class Papi_Lib_Core_Template_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create( [
			'post_type' => 'page'
		] );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_body_class() {
		global $post;

		$this->assertEmpty( papi_body_class( [] ) );

		$post = get_post( $this->post_id );
		$this->go_to( get_permalink( $this->post_id ) );
		$this->assertEmpty( papi_body_class( [] ) );

		update_post_meta( $this->post_id, papi_get_page_type_key(), '/' );
		$this->assertEmpty( papi_body_class( [] ) );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertSame( [ 'simple-page-type' ], papi_body_class( [] ) );
	}

	public function test_papi_get_template_file_name() {
		$this->assertNull( papi_get_template_file_name( '' ) );
		$this->assertNull( papi_get_template_file_name( true ) );
		$this->assertNull( papi_get_template_file_name( false ) );
		$this->assertNull( papi_get_template_file_name( null ) );
		$this->assertNull( papi_get_template_file_name( 1 ) );
		$this->assertNull( papi_get_template_file_name( 0 ) );
		$this->assertNull( papi_get_template_file_name( '' ) );
		$this->assertNull( papi_get_template_file_name( [] ) );
		$this->assertNull( papi_get_template_file_name( (object) [] ) );

		$this->assertSame( 'hello/world.php', papi_get_template_file_name( 'hello.world' ) );
		$this->assertSame( 'hello/world.php', papi_get_template_file_name( 'hello/world' ) );
		$this->assertSame( 'hello/world.php', papi_get_template_file_name( 'hello/world.php' ) );
	}

	public function test_papi_include_template() {
		$this->assertEmpty( papi_include_template( '' ) );
		$this->assertEmpty( papi_include_template( 1 ) );
		$this->assertEmpty( papi_include_template( null ) );
		$this->assertEmpty( papi_include_template( [] ) );
		$this->assertEmpty( papi_include_template( new stdClass ) );
		$this->assertEmpty( papi_include_template( true ) );
		$this->assertEmpty( papi_include_template( false ) );
		$this->assertEmpty( papi_include_template( 'path/to/fake/file.php' ) );

		papi_include_template( 'admin/views/add-new-page.php' );
		$this->expectOutputRegex( '/Add New Page/' );
	}

	public function test_papi_template() {
		$template = papi_template( PAPI_FIXTURE_DIR . '/properties/simple.php', [
			'lang' => 'se'
		] );

		$this->assertSame( 'Name', $template->title );
		$this->assertSame( 'Name', $template->get_option( 'title' ) );
		$this->assertSame( 'string', $template->type );
		$this->assertSame( 'string', $template->get_option( 'type' ) );
		$this->assertSame( 'se', $template->lang );
		$this->assertSame( 'se', $template->get_option( 'lang' ) );

		$this->assertEmpty( papi_template( '' ) );
		$this->assertEmpty( papi_template( null ) );
		$this->assertEmpty( papi_template( true ) );
		$this->assertEmpty( papi_template( false ) );
		$this->assertEmpty( papi_template( 1 ) );
		$this->assertEmpty( papi_template( [] ) );
		$this->assertEmpty( papi_template( new stdClass() ) );
		$this->assertEmpty( papi_template( PAPI_FIXTURE_DIR ) );

		$template = papi_template( PAPI_FIXTURE_DIR . '/properties/array.php', [], true );

		$this->assertSame( 'Name', $template->title );
		$this->assertSame( 'string', $template->type );

		$this->assertEmpty( papi_template( 'hello' ) );
	}

	public function test_papi_template_include() {
		global $post;

		$this->assertEmpty( apply_filters( 'template_include', '' ) );

		$post = get_post( $this->post_id );
		$this->go_to( get_permalink( $this->post_id ) );
		$this->assertEmpty( apply_filters( 'template_include', '' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertEmpty( apply_filters( 'template_include', '' ) );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'twenty-page-type' );
		$this->flush_cache();

		$path = get_template_directory();
		$path = trailingslashit( $path );
		$path = apply_filters( 'template_include', '' );
		$this->assertNotFalse( strpos( $path, 'functions.php' ) );
	}

	public function test_filter_papi_template_include() {
		global $post;

		$this->assertEmpty( apply_filters( 'papi/template_include', '' ) );

		$post = get_post( $this->post_id );
		$this->go_to( get_permalink( $this->post_id ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		tests_add_filter( 'papi/template_include', function () {
			return 'purus-risus-adipiscing-pharetramollis.php';
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertEmpty( apply_filters( 'template_include', '' ) );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'twenty-page-type' );
		$this->flush_cache();

		$this->assertSame( 'purus-risus-adipiscing-pharetramollis.php', apply_filters( 'template_include', '' ) );

	}
}
