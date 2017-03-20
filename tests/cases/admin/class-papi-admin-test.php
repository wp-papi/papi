<?php

/**
 * @group admin
 */
class Papi_Admin_Test extends WP_UnitTestCase {

	/**
	 * @var Papi_Admin
	 */
	protected $admin;

	/**
	 * @var int
	 */
	protected $post_id;

	public function setUp() {
		parent::setUp();
		$this->admin = new Papi_Admin;
		$this->post_id = $this->factory->post->create();

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->admin, $this->post_id );
	}

	public function register_template_paths( $new_templates ) {
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = [];
		}

		wp_cache_delete( $cache_key , 'themes' );
		$templates = array_merge( $templates, $new_templates );
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $new_templates;
	}

	public function test_admin_body_class() {
		papi()->reset();
		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/page/simple-page-type';

		$classes = $this->admin->admin_body_class( '' );
		$this->assertTrue( (bool) preg_match( '/\spapi\-body papi\-meta\-type\-post/', $classes ) );
	}

	public function test_admin_body_class_with_entry_type_body_classes() {
		papi()->reset();
		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/page/simple-page-type';
		$admin = new Papi_Admin;
		$classes = $admin->admin_body_class( '' );
		$this->assertTrue( (bool) preg_match( '/\ssimple\-page\-type/', $classes ) );
	}

	public function test_admin_init() {
		$admin = new Papi_Admin;
		$this->assertNull( $admin->admin_init() );

		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/page/simple-page-type';
		$admin = new Papi_Admin;
		$admin->admin_init();
	}

	public function test_edit_form_after_title() {
		$this->admin->edit_form_after_title();
		$this->expectOutputRegex( '/papi\_meta\_nonce/' );
	}

	public function test_edit_form_after_title_2() {
		$_GET['entry_type'] = 'test';
		$this->admin->edit_form_after_title();
		$this->expectOutputRegex( '/name\=\"\_papi\_page\_type\"/' );
	}

	public function test_plugin_row_meta() {
		$output = $this->admin->plugin_row_meta( [], 'fake/fake.php' );
		$this->assertEmpty( $output );

		$testroot = basename( dirname( PAPI_PLUGIN_DIR ) );
		$output = $this->admin->plugin_row_meta( [], $testroot . '/papi-loader.php' );
		$this->assertArrayHasKey( 'docs', $output );
	}

	public function test_setup_actions() {
		global $current_screen;

	    $current_screen = WP_Screen::get( 'admin_init' );

		$admin = new Papi_Admin;

		$this->assertSame( 10, has_action( 'admin_init', [$admin, 'admin_init'] ) );

		$_GET['taxonomy'] = 'post_tag';
		$admin = new Papi_Admin;

		$this->assertSame( 10, has_action( 'post_tag_add_form', [$admin, 'edit_form_after_title'] ) );
		$this->assertSame( 10, has_action( 'post_tag_edit_form', [$admin, 'edit_form_after_title'] ) );

		$current_screen = null;
	}

	public function test_setup_filters() {
		global $current_screen;

	    $current_screen = WP_Screen::get( 'admin_init' );
		$admin = new Papi_Admin;

		$this->assertSame( 10, has_filter( 'admin_body_class', [$admin, 'admin_body_class'] ) );

		$current_screen = null;
	}

/*
	public function test_setup_papi() {
		$admin = new Papi_Admin;
		$this->assertFalse( $admin->setup_papi() );
		$_GET['post_type'] = 'revision';
		$admin = new Papi_Admin;
		$this->assertFalse( $admin->setup_papi() );
		$_GET['post_type'] = 'nav_menu_item';
		$admin = new Papi_Admin;
		$this->assertFalse( $admin->setup_papi() );

		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/page/simple-page-type';
		$admin = new Papi_Admin;
		$this->assertTrue( $admin->setup_papi() );

		unset( $_GET['page'] );

		$_GET['post_type'] = 'attachment';
		$admin = new Papi_Admin;
		$this->assertTrue( $admin->setup_papi() );
	}
*/
	public function test_wp_link_query() {
		$admin = new Papi_Admin;
		$post  = [
			'ID'   => $this->post_id,
			'info' => 'Page'
		];
		$post2 = [
			'ID'   => $this->post_id,
			'info' => 'Standard Page'
		];
		$results = $admin->wp_link_query( [$post] );
		$this->assertSame( [$post2], $results );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		$post3 = [
			'ID'   => $this->post_id,
			'info' => 'Simple page'
		];
		$results = $admin->wp_link_query( [$post] );
		$this->assertSame( [$post3], $results );
	}

	public function test_wp_refresh_nonces() {
		$admin = new Papi_Admin;

		$arr = [];
		$this->assertEmpty( $admin->wp_refresh_nonces( $arr ) );

		$arr = [
			'wp-refresh-post-nonces' => [
				'replace' => []
			]
		];

		$arr2 = $admin->wp_refresh_nonces( $arr );

		$this->assertArrayHasKey( 'papi_meta_nonce', $arr2['wp-refresh-post-nonces']['replace'] );
	}
}
