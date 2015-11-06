<?php

class Papi_Lib_Types_Page_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_display_page_type() {
		$this->assertFalse( papi_display_page_type( 'fake-page-type1' ) );

		$_GET['post_type'] = 'page';
		$this->assertFalse( papi_display_page_type( 'fake-page-type2' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type = papi_get_page_type_by_id( 'display-not-page-type' );
		$this->assertFalse( papi_display_page_type( $page_type ) );

		$page_type = papi_get_page_type_by_id( 'empty-page-type' );
		$this->assertTrue( papi_display_page_type( $page_type ) );

		tests_add_filter( 'papi/settings/show_page_type_page', function ( $page_type ) {
			if ( $page_type == 'simple-page-type' ) {
				return false;
			}

			return true;
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertFalse( papi_display_page_type( $page_type ) );

		$GET['post_type'] = 'module';

		$page_type = papi_get_page_type_by_id( 'faq-page-type' );
		$this->assertFalse( papi_display_page_type( $page_type ) );

		$type          = 'papi-standard-page-type';
		$page_type     = new Papi_Page_Type( $type );
		$page_type->id = $type;
		$this->assertTrue( papi_display_page_type( $page_type ) );
	}

	public function test_display_for_child_page_type() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$_GET['post_type'] = 'page';
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, papi_get_page_type_key(), 'faq-page-type' );

		$_GET['post_parent'] = $post_id;
		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertTrue( papi_display_page_type( $page_type ) );
	}

	public function test_display_for_child_page_type_2() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$post_id1 = $this->factory->post->create( ['post_type' => 'page'] );
		$post_id2 = $this->factory->post->create( ['post_type' => 'page', 'parent_post' => $post_id1] );

		$_GET['post_type'] = 'page';
		$_GET['post_parent'] = $post_id1;

		update_post_meta( $post_id1, papi_get_page_type_key(), 'simple-page-type' );
		update_post_meta( $post_id2, papi_get_page_type_key(), 'simple-page-type' );

		$page_type = papi_get_page_type_by_post_id( $post_id2 );
		$this->assertTrue( papi_display_page_type( $page_type ) );
	}

	public function test_papi_get_all_page_types() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->assertNotEmpty( papi_get_all_page_types() );
		$this->assertSame( 1, count( papi_get_all_page_types( 'book' ) ) );
	}

	public function test_papi_get_page_type_by_post_id() {
		$this->assertNull( papi_get_page_type_by_post_id( 0 ) );
		$this->assertNull( papi_get_page_type_by_post_id( [] ) );
		$this->assertNull( papi_get_page_type_by_post_id( (object) [] ) );
		$this->assertNull( papi_get_page_type_by_post_id( true ) );
		$this->assertNull( papi_get_page_type_by_post_id( false ) );
		$this->assertNull( papi_get_page_type_by_post_id( null ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$this->assertTrue( is_object( papi_get_page_type_by_post_id( $this->post_id ) ) );

		$_GET['page_id'] = $this->post_id;
		$this->assertTrue( is_object( papi_get_page_type_by_post_id() ) );
		unset( $_GET['page_id'] );
	}

	public function test_papi_get_number_of_pages() {
		$this->assertSame( 0, papi_get_number_of_pages( 'simple-page-type' ) );
		$this->assertSame( 0, papi_get_number_of_pages( null ) );
		$this->assertSame( 0, papi_get_number_of_pages( true ) );
		$this->assertSame( 0, papi_get_number_of_pages( false ) );
		$this->assertSame( 0, papi_get_number_of_pages( [] ) );
		$this->assertSame( 0, papi_get_number_of_pages( new stdClass() ) );
		$this->assertSame( 0, papi_get_number_of_pages( 1 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->flush_cache();
		$this->assertSame( 1, papi_get_number_of_pages( 'simple-page-type' ) );

		$simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );

		$this->assertSame( 1, papi_get_number_of_pages( $simple_page_type ) );
	}

	public function test_papi_get_page_type_template() {
		$this->assertNull( papi_get_page_type_template() );
		$this->assertNull( papi_get_page_type_template( 0 ) );
		$this->assertNull( papi_get_page_type_template( null ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$this->flush_cache();
		$actual = papi_get_page_type_template( $this->post_id );
		$this->assertSame( 'pages/simple-page.php', $actual );

		$_GET['page_id'] = $this->post_id;
		$this->assertSame( 'pages/simple-page.php', papi_get_page_type_template() );
		unset( $_GET['page_id'] );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'dot-page-type' );

		$this->flush_cache();
		$actual = papi_get_page_type_template( $this->post_id );
		$this->assertSame( 'pages/dot.php', $actual );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'dot2-page-type' );

		$this->flush_cache();
		$actual = papi_get_page_type_template( $this->post_id );
		$this->assertSame( 'pages/dot2.php', $actual );
	}

	public function test_papi_get_page_type_by_id() {
		$this->assertNull( papi_get_page_type_by_id( 0 ) );
		$this->assertNull( papi_get_page_type_by_id( [] ) );
		$this->assertNull( papi_get_page_type_by_id( (object) [] ) );
		$this->assertNull( papi_get_page_type_by_id( true ) );
		$this->assertNull( papi_get_page_type_by_id( false ) );
		$this->assertNull( papi_get_page_type_by_id( null ) );
		$this->assertNull( papi_get_page_type_by_id( 'page' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertTrue( is_object( $simple_page_type ) );
	}

	public function test_papi_get_page_type_id() {
		$this->assertEmpty( papi_get_page_type_id() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertSame( 'simple-page-type', papi_get_page_type_id( $this->post_id ) );

		$_GET['page_type'] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_page_type_id() );
		unset( $_GET['page_type'] );

		$_POST[PAPI_PAGE_TYPE_KEY] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_page_type_id() );
		unset( $_POST[PAPI_PAGE_TYPE_KEY] );

		$post_parent = 'post_parent';
		$_GET[$post_parent] = $this->post_id;
		$this->assertSame( 'simple-page-type', papi_get_page_type_id() );
		unset( $_GET[$post_parent] );

		$this->assertEmpty( papi_get_page_type_id() );
	}

	public function test_papi_get_page_type_key() {
		$this->assertSame( '_papi_page_type', papi_get_page_type_key() );
	}

	public function test_papi_get_page_type_name() {
		$this->assertEmpty( papi_get_page_type_name() );
		$this->assertEmpty( papi_get_page_type_name( null ) );
		$this->assertEmpty( papi_get_page_type_name( 0 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		global $post;
		$post = get_post( $this->post_id );

		$this->assertSame( 'Simple page', papi_get_page_type_name() );
		$this->assertSame( 'Simple page', papi_get_page_type_name( $this->post_id ) );
	}

	public function test_papi_is_page_type() {
		$this->assertTrue( papi_is_page_type( new Papi_Page_Type ) );
		$this->assertFalse( papi_is_page_type( new Papi_Option_Type ) );
		$this->assertFalse( papi_is_page_type( true ) );
		$this->assertFalse( papi_is_page_type( false ) );
		$this->assertFalse( papi_is_page_type( null ) );
		$this->assertFalse( papi_is_page_type( 1 ) );
		$this->assertFalse( papi_is_page_type( 0 ) );
		$this->assertFalse( papi_is_page_type( '' ) );
		$this->assertFalse( papi_is_page_type( [] ) );
		$this->assertFalse( papi_is_page_type( (object) [] ) );
	}

	public function test_papi_set_page_type_id() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->assertFalse( papi_set_page_type_id( 0, 'hello' ) );
		$post_id = $this->factory->post->create();
		$this->assertFalse( papi_set_page_type_id( $post_id, 'hello' ) );
		$this->assertNotFalse( papi_set_page_type_id( $post_id, 'empty-page-type' ) );
		$this->assertSame( 'empty-page-type', papi_get_page_type_id( $post_id ) );
	}

	public function test_the_papi_page_type_name() {
		the_papi_page_type_name();
		$this->expectOutputRegex( '//' );

		the_papi_page_type_name( null );
		$this->expectOutputRegex( '//' );

		the_papi_page_type_name( 0 );
		$this->expectOutputRegex( '//' );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		global $post;
		$post = get_post( $this->post_id );

		the_papi_page_type_name();
		$this->expectOutputRegex( '/Simple\spage/' );

		the_papi_page_type_name( $this->post_id );
		$this->expectOutputRegex( '/Simple\spage/' );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '' );
		the_papi_page_type_name();
		$this->expectOutputRegex( '//' );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'random322-page-type' );
		the_papi_page_type_name();
		$this->expectOutputRegex( '//' );
	}
}
