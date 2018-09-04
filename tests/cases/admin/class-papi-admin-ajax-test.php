<?php

/**
 * @group ajax
 */
class Papi_Admin_Ajax_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET  = [];
		$_POST = [];
		$this->ajax = new Papi_Admin_Ajax();
		add_filter( 'wp_die_ajax_handler', [$this, 'get_wp_die_handler'], 1, 1 );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $_POST, $this->ajax );
		remove_filter( 'wp_die_ajax_handler', [$this, 'get_wp_die_handler'], 1, 1 );
	}

	public function wp_die_handler( $message ) {
	}

	public function test_actions() {
		$this->assertSame( 10, has_action( 'init', [$this->ajax, 'add_endpoint'] ) );
		$this->assertSame( 10, has_action( 'parse_request', [$this->ajax, 'handle_papi_ajax'] ) );
		$this->assertSame( 10, has_action( 'admin_enqueue_scripts', [$this->ajax, 'ajax_url'] ) );
		$this->assertSame( 10, has_action( 'papi/ajax/get_property', [$this->ajax, 'get_property'] ) );
		$this->assertSame( 10, has_action( 'papi/ajax/get_properties', [$this->ajax, 'get_properties'] ) );
	}

	public function test_endpoint() {
		$ajax = new Papi_Admin_Ajax();
		$ajax->add_endpoint();

		global $wp_rewrite;
		$this->assertTrue( ! isset( $wp_rewrite->extra_rules_top['papi-ajax/([^/]*)/?'] ) );

		add_filter( 'pre_option_permalink_structure', function() {
			return 'not empty';
		} );

		$ajax = new Papi_Admin_Ajax();
		$ajax->add_endpoint();

		$this->assertNotNull( $wp_rewrite->extra_rules_top['papi-ajax/([^/]*)/?'] );
		$this->assertSame( 'index.php?action=$matches[1]', $wp_rewrite->extra_rules_top['papi-ajax/([^/]*)/?'] );
	}

	public function test_handle_papi_ajax_doing_ajax() {
		$this->assertNull( $this->ajax->handle_papi_ajax() );
	}

	public function test_handle_papi_ajax_action() {
		$_GET = [
			'action' => 'get_property',
			'slug'   => 'hello',
			'type'   => 'string'
		];

		$user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		$this->ajax->handle_papi_ajax();
		wp_set_current_user( 0 );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/papi\_hello/' );
	}

	public function test_handle_papi_ajax() {
		$this->assertNull( $this->ajax->handle_papi_ajax() );
		$this->expectOutputRegex( '//' );
	}

	public function test_get_entry_type_fail() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_GET = [
			'entry_type' => 'properties-page-type'
		];

		do_action( 'papi/ajax/get_entry_type' );

		$this->expectOutputRegex( '/\{\"error\"\:\"No entry type found\"\}/' );
	}

	public function test_get_entry_type_success() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_GET = [
			'entry_type' => 'properties-page-type'
		];

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		do_action( 'papi/ajax/get_entry_type' );

		$this->expectOutputRegex( '/properties\-page/' );
	}

	public function test_get_posts() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$post_id = $this->factory->post->create();

		do_action( 'papi/ajax/get_posts' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( sprintf( '/\"ID\"\:%d\,/', $post_id ) );
	}

	public function test_get_posts_fields() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$post_id = $this->factory->post->create();

		$_GET = [
			'fields' => ['ID']
		];

		do_action( 'papi/ajax/get_posts' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( sprintf( '/\{\"ID\"\:%d\}/', $post_id ) );
	}

	public function test_get_posts_fake_post_type() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_GET = [
			'query' => [
				'post_type' => 'get_posts'
			]
		];

		do_action( 'papi/ajax/get_posts' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\[\]/' );
	}

	public function test_get_property() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_GET = [
			'slug' => 'hello',
			'type' => 'string'
		];

		do_action( 'papi/ajax/get_property' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/papi\_hello/' );
	}

	public function test_get_property_fail() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_GET = [
			'slug' => 'hello',
			'type' => 'fake'
		];

		do_action( 'papi/ajax/get_property' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No property found\"\}/' );
	}

	public function test_get_properties() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$property = papi_get_property_type( [
			'slug' => 'name',
			'type' => 'string'
		] );

		$_POST = [
			'properties' => json_encode( [
				$property->get_options(),
				[
					'type'  => 'string',
					'title'	=> 'nyckel'
				]
			] )
		];

		do_action( 'papi/ajax/get_properties' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/papi\_name/' );
		$this->expectOutputRegex( '/papi\_nyckel/' );
	}

	public function test_get_properties_fail() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_POST = [
			'properties' => json_encode( [
				[
					'type'  => 'fake',
					'title'	=> 'nyckel'
				]
			] )
		];

		do_action( 'papi/ajax/get_properties' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No properties found\"\}/' );
	}

	public function test_get_properties_fail_2() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_POST = [];

		do_action( 'papi/ajax/get_properties' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No properties found\"\}/' );
	}

	public function test_get_properties_fail_3() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_POST = [
			'properties' => json_encode( [] )
		];

		do_action( 'papi/ajax/get_properties' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No properties found\"\}/' );
	}

	public function test_get_rules_result_success() {
		$_GET = [
			'page_type' => 'rule-page-type',
			'post'      => $this->factory->post->create()
		];
		$_POST = [
			'data' => json_encode( [
				'rules' => [
					[
						'operator' => '=',
						'slug'     => 'rules1',
						'source'   => '',
						'value'	   => 123
						]
					],
				'slug'  => 'rules_1'
			] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"render\"\:false\}/' );
	}

	public function test_get_rules_result_success_2() {
		$_GET = [
			'page_type' => 'rule-page-type',
			'post'      => $this->factory->post->create()
		];
		$_POST = [
			'data' => json_encode( [
				'rules' => [
					[
						'operator' => 'NOT EXISTS',
						'slug'     => 'rules2',
						'source'   => ''
					]
				],
				'slug'  => 'rules_2'
			] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"render\"\:true\}/' );
	}

	public function test_get_rules_result_success_3() {
		$_GET = [
			'page_type' => 'rule-page-type',
			'post'      => $this->factory->post->create()
		];
		$_POST = [
			'data' => json_encode( [
				'rules' => [
					[
						'operator' => '=',
						'slug'     => 'rules_3',
						'source'   => 'hello',
						'value'    => 'hello'
					]
				],
				'slug'  => 'rules_3'
			] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		if ( ! defined( 'DOING_PAPI_AJAX' ) ) {
			define( 'DOING_PAPI_AJAX', true );
		}

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"render\"\:true\}/' );
	}

	public function test_get_rules_result_fail() {
		$_GET = [
			'slug' => 'name'
		];

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No rule found\"\}/' );
	}

	public function test_get_rules_result_fail_2() {
		$_GET = [
			'page_type' => 'name'
		];

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No rule found\"\}/' );
	}

	public function test_get_rules_result_fail_3() {
		$_GET = [
			'slug'      => 'name',
			'page_type' => 'fake'
		];

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No rule found\"\}/' );
	}

	public function test_get_rules_result_fail_4() {
		$_GET = [
			'page_type' => 'simple-page-type'
		];

		$_POST = [
			'data' => json_encode( [
				'slug' => 'fake'
			] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		if ( ! defined( 'DOING_PAPI_AJAX' ) ) {
			define( 'DOING_PAPI_AJAX', true );
		}

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No rule found\"\}/' );
	}

	public function test_get_rules_result_fail_5() {
		$_GET = [
			'page_type' => 'simple-page-type'
		];

		$_POST = [
			'data' => json_encode( [] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		if ( ! defined( 'DOING_PAPI_AJAX' ) ) {
			define( 'DOING_PAPI_AJAX', true );
		}

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No rule found\"\}/' );
	}

	public function test_get_rules_result_fail_6() {
		$_GET = [
			'page_type' => 'rule-page-type',
			'post'      => $this->factory->post->create()
		];
		$_POST = [
			'data' => json_encode( [
				'rules' => [
					[
						'operator' => '=',
						'slug'     => 'rules_4',
						'source'   => 'fredrik',
						'value'    => 'hello'
					]
				],
				'slug'  => 'rules_3'
			] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		if ( ! defined( 'DOING_PAPI_AJAX' ) ) {
			define( 'DOING_PAPI_AJAX', true );
		}

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"render\"\:false\}/' );
	}

	public function test_get_rules_result_fail_7() {
		$_GET = [
			'page_type' => 'rule-page-type',
			'post'      => $this->factory->post->create()
		];
		$_POST = [
			'data' => json_encode( [
				'rules' => [
					[
						'operator' => '=',
						'slug'     => 'rules_4',
						'source'   => 'fredrik',
						'value'    => 'hello'
					]
				],
				'slug'  => 'rules_3[]'
			] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		if ( ! defined( 'DOING_PAPI_AJAX' ) ) {
			define( 'DOING_PAPI_AJAX', true );
		}

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"render\"\:false\}/' );
	}

	public function test_get_rules_result_fail_8() {
		$_GET = [
			'page_type' => 'rule-page-type',
			'post'      => $this->factory->post->create()
		];
		$_POST = [
			'data' => json_encode( [
				'rules' => [
					[
						'operator' => '=',
						'slug'     => 'rules_fake',
						'source'   => 'fredrik',
						'value'    => 'hello'
					]
				],
				'slug'  => 'rules_4'
			] )
		];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		if ( ! defined( 'DOING_PAPI_AJAX' ) ) {
			define( 'DOING_PAPI_AJAX', true );
		}

		do_action( 'papi/ajax/get_rules_result' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No rule found\"\}/' );
	}

	public function test_get_shortcode_fail() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_GET = ['action' => 'get_shortcode'];

		do_action( 'papi/ajax/get_shortcode' );

		$this->expectOutputRegex( '/{\"html\"\:\"\"\}/' );
	}

	public function test_get_shortcode_success() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		add_shortcode( 'hello', function () {
			return 'hello';
		} );

		$_GET = ['action' => 'get_shortcode', 'shortcode' => '[hello]'];

		do_action( 'papi/ajax/get_shortcode' );

		$this->expectOutputRegex( '/\"hello\"/' );
	}

	public function test_get_terms() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		register_taxonomy( 'test_taxonomy', 'post' );

		$term_id = $this->factory->term->create( [
			'taxonomy'    => 'test_taxonomy',
			'name'        => 'Apple',
			'description' => 'A yummy apple'
		] );

		$_GET = [
			'taxonomy' => 'test_taxonomy'
		];

		$post_id = $this->factory->post->create();
		wp_set_object_terms( $post_id, $term_id, 'test_taxonomy' );

		do_action( 'papi/ajax/get_terms' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( sprintf( '/{\"%s\"\:\"Apple\"\}/', $term_id ) );
	}

	public function test_render_error() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$this->ajax->render_error( 'No property found' );
		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/\{\"error\"\:\"No property found\"\}/' );
	}
}
